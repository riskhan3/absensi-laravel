<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Teacher\Concerns\ResolvesTeacher;
use App\Http\Controllers\Teacher\Concerns\SyncsToStudentAttendance;
use App\Models\AttendanceSession;
use App\Models\AttendanceStatus;
use App\Models\Classroom;
use App\Models\SessionStudentAttendance;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClassroomScanController extends Controller
{
    use ResolvesTeacher, SyncsToStudentAttendance;

    /** Halaman scanner absensi — tidak perlu data kelas guru */
    public function index()
    {
        $teacher = $this->resolveTeacher();
        $user    = Auth::user();

        $subjects = $this->availableSubjects($teacher, $user);
        $today    = now()->toDateString();

        $attendanceStatuses = AttendanceStatus::where('name', '!=', 'Hadir')->get(['id', 'name']);
        $haidirId = AttendanceStatus::where('name', 'Hadir')->value('id');
        $alphaId  = AttendanceStatus::where('name', 'Tanpa Keterangan')->value('id');

        return view('teacher.classroom-scan.index', compact(
            'subjects', 'today', 'teacher',
            'attendanceStatuses', 'haidirId', 'alphaId'
        ));
    }

    /**
     * Proses scan satu siswa (QR atau RFID).
     * Kelas diambil OTOMATIS dari data siswa — tidak perlu dipilih guru.
     */
    public function scan(Request $request): JsonResponse
    {
        $request->validate([
            'scan_code'    => ['required', 'string'],
            'date'         => ['required', 'date'],
            'subject_ids'  => ['required', 'array', 'min:1'],
            'subject_ids.*'=> ['exists:subjects,id'],
        ]);

        $code = trim($request->scan_code);

        // Cari siswa berdasarkan unique_code ATAU rfid_code
        $student = Student::where('unique_code', $code)
            ->orWhere('rfid_code', $code)
            ->with('classroom')
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'code'    => 'NOT_FOUND',
                'message' => "Kode tidak ditemukan: {$code}",
            ], 404);
        }

        if (!$student->classroom) {
            return response()->json([
                'success' => false,
                'code'    => 'NO_CLASS',
                'message' => "{$student->name} belum memiliki kelas di data siswa.",
            ], 422);
        }

        $teacher = $this->resolveTeacher();

        // Auto buat/ambil sesi untuk kelas siswa ini
        $session = AttendanceSession::firstOrCreate(
            [
                'classroom_id' => $student->classroom_id,
                'teacher_id'   => $teacher->id,
                'date'         => $request->date,
            ],
            ['notes' => null]
        );

        // Sync mata pelajaran ke sesi
        $session->subjects()->sync($request->subject_ids);

        $haidirStatus = AttendanceStatus::where('name', 'Hadir')->first();

        // Cek sudah discan?
        $existing = SessionStudentAttendance::where('attendance_session_id', $session->id)
            ->where('student_id', $student->id)->first();

        if ($existing && $existing->attendance_status_id === $haidirStatus->id) {
            return response()->json([
                'success' => false,
                'code'    => 'ALREADY_SCANNED',
                'message' => "{$student->name} sudah absen.",
                'student' => ['id' => $student->id, 'name' => $student->name, 'nis' => $student->nis],
            ], 422);
        }

        // Simpan Hadir
        SessionStudentAttendance::updateOrCreate(
            ['attendance_session_id' => $session->id, 'student_id' => $student->id],
            ['attendance_status_id' => $haidirStatus->id, 'notes' => null]
        );

        $this->syncSingleAttendance($session, $student->id, $haidirStatus->id);

        return response()->json([
            'success'        => true,
            'session_id'     => $session->id,
            'classroom_name' => $student->classroom->full_name,
            'student'        => [
                'id'             => $student->id,
                'name'           => $student->name,
                'nis'            => $student->nis,
                'classroom_name' => $student->classroom->full_name,
            ],
            'message' => "✅ {$student->name} — Hadir",
        ]);
    }

    /**
     * Ambil data review: daftar semua siswa per sesi (per kelas)
     * beserta status scan mereka, untuk fase review.
     */
    public function review(Request $request): JsonResponse
    {
        $request->validate([
            'session_ids'   => ['required', 'array', 'min:1'],
            'session_ids.*' => ['exists:attendance_sessions,id'],
        ]);

        $haidirId = AttendanceStatus::where('name', 'Hadir')->value('id');
        $alphaId  = AttendanceStatus::where('name', 'Tanpa Keterangan')->value('id');

        $sessions = [];
        foreach ($request->session_ids as $sessionId) {
            $session = AttendanceSession::with('classroom')->find($sessionId);
            if (!$session) continue;

            $students = Student::where('classroom_id', $session->classroom_id)
                ->select('id', 'nis', 'name')
                ->orderBy('name')
                ->get();

            $scannedIds = SessionStudentAttendance::where('attendance_session_id', $session->id)
                ->where('attendance_status_id', $haidirId)
                ->pluck('student_id')
                ->toArray();

            $sessions[] = [
                'session_id'     => $session->id,
                'classroom_name' => $session->classroom->full_name,
                'students'       => $students,
                'scanned_ids'    => $scannedIds,
                'alpha_id'       => $alphaId,
            ];
        }

        return response()->json(['success' => true, 'sessions' => $sessions]);
    }

    /**
     * Finalisasi — simpan status absensi semua siswa di semua sesi,
     * redirect ke halaman detail sesi pertama.
     */
    public function finalize(Request $request): JsonResponse
    {
        $request->validate([
            'attendances'              => ['required', 'array'],
            'attendances.*.session_id' => ['required', 'exists:attendance_sessions,id'],
            'attendances.*.student_id' => ['required', 'exists:students,id'],
            'attendances.*.status_id'  => ['required', 'exists:attendance_statuses,id'],
            'notes'                    => ['nullable', 'string'],
        ]);

        $grouped = collect($request->attendances)->groupBy('session_id');
        $firstSessionId = null;

        DB::transaction(function () use ($grouped, $request, &$firstSessionId) {
            foreach ($grouped as $sessionId => $items) {
                $session = AttendanceSession::find($sessionId);
                if (!$session) continue;

                if (!$firstSessionId) $firstSessionId = $session->id;

                foreach ($items as $item) {
                    SessionStudentAttendance::updateOrCreate(
                        [
                            'attendance_session_id' => $session->id,
                            'student_id'            => $item['student_id'],
                        ],
                        ['attendance_status_id' => $item['status_id']]
                    );
                }

                if ($request->notes) {
                    $session->update(['notes' => $request->notes]);
                }

                $this->syncSessionToStudentAttendances($session);
            }
        });

        return response()->json([
            'success'      => true,
            'redirect_url' => route('teacher.sessions.show', $firstSessionId),
        ]);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function availableSubjects(?Teacher $teacher, $user)
    {
        if ($user->role === 'wali_kelas') {
            return Subject::where('is_active', true)->orderBy('class_group')->orderBy('name')->get();
        }
        if (!$teacher) return Subject::where('is_active', true)->get();
        $subjects = $teacher->subjects()->where('is_active', true)->get();
        return $subjects->isNotEmpty() ? $subjects : Subject::where('is_active', true)->orderBy('name')->get();
    }
}
