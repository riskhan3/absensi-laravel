<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Teacher\Concerns\ResolvesTeacher;
use App\Http\Controllers\Teacher\Concerns\SyncsToStudentAttendance;
use App\Models\AttendanceSession;
use App\Models\AttendanceStatus;
use App\Models\Classroom;
use App\Models\SessionStudentAttendance;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceSessionController extends Controller
{
    use ResolvesTeacher, SyncsToStudentAttendance;
    /** Daftar sesi absen milik guru yang login */
    public function index(Request $request)
    {
        $teacher  = $this->resolveTeacher();
        $sessions = AttendanceSession::with(['classroom', 'subjects', 'studentAttendances.status'])
            ->where('teacher_id', $teacher?->id)
            ->orderByDesc('date')
            ->paginate(20);

        return view('teacher.sessions.index', compact('sessions', 'teacher'));
    }

    /** Form buat sesi absen baru */
    public function create(Request $request)
    {
        $teacher = $this->resolveTeacher();

        // Semua guru bisa memilih dari semua kelas yang ada
        $classrooms = Classroom::orderByRaw("FIELD(grade,'I','II','III','IV','V','VI')")
            ->with('students')->get();

        // Semua guru bisa memilih semua mata pelajaran aktif
        $subjects = Subject::where('is_active', true)
            ->orderBy('class_group')->orderBy('name')->get();

        $statuses = AttendanceStatus::all();
        $today    = now()->toDateString();

        return view('teacher.sessions.create', compact('classrooms', 'subjects', 'statuses', 'today', 'teacher'));
    }

    /** Simpan sesi absen beserta detail per siswa */
    public function store(Request $request)
    {
        $request->validate([
            'classroom_id'     => ['required', 'exists:classrooms,id'],
            'date'             => ['required', 'date'],
            'subject_ids'      => ['required', 'array', 'min:1'],
            'subject_ids.*'    => ['exists:subjects,id'],
            'attendances'      => ['required', 'array'],
            'attendances.*.status_id' => ['required', 'exists:attendance_statuses,id'],
        ]);

        $teacher = $this->resolveTeacher();

        DB::transaction(function () use ($request, $teacher) {
            // Buat atau update sesi
            $session = AttendanceSession::firstOrCreate(
                [
                    'classroom_id' => $request->classroom_id,
                    'teacher_id'   => $teacher->id,
                    'date'         => $request->date,
                ],
                ['notes' => $request->notes]
            );

            // Sync mata pelajaran
            $session->subjects()->sync($request->subject_ids);

            // Simpan absen tiap siswa
            foreach ($request->attendances as $studentId => $data) {
                SessionStudentAttendance::updateOrCreate(
                    [
                        'attendance_session_id' => $session->id,
                        'student_id'            => $studentId,
                    ],
                    [
                        'attendance_status_id' => $data['status_id'],
                        'notes'                => $data['notes'] ?? null,
                    ]
                );
            }

            // ── Sync ke student_attendances agar tampil di laporan ──
            $this->syncSessionToStudentAttendances($session);
        });

        return redirect()->route('teacher.sessions.index')
            ->with('success', 'Absensi berhasil disimpan.');
    }

    /** Detail sesi absen */
    public function show(AttendanceSession $session)
    {
        $session->load(['classroom', 'subjects', 'teacher',
                        'studentAttendances.student', 'studentAttendances.status']);

        return view('teacher.sessions.show', compact('session'));
    }

    /** Form edit sesi */
    public function edit(AttendanceSession $session)
    {
        $teacher    = $this->resolveTeacher();
        $user       = Auth::user();
        $session->load(['classroom.students', 'subjects', 'studentAttendances.status']);

        $subjects = $this->availableSubjects($teacher, $user);
        $statuses = AttendanceStatus::all();

        return view('teacher.sessions.edit', compact('session', 'subjects', 'statuses'));
    }

    /** Update sesi absen */
    public function update(Request $request, AttendanceSession $session)
    {
        $request->validate([
            'subject_ids'      => ['required', 'array', 'min:1'],
            'subject_ids.*'    => ['exists:subjects,id'],
            'attendances'      => ['required', 'array'],
            'attendances.*.status_id' => ['required', 'exists:attendance_statuses,id'],
        ]);

        DB::transaction(function () use ($request, $session) {
            $session->subjects()->sync($request->subject_ids);

            foreach ($request->attendances as $studentId => $data) {
                SessionStudentAttendance::updateOrCreate(
                    ['attendance_session_id' => $session->id, 'student_id' => $studentId],
                    ['attendance_status_id' => $data['status_id'], 'notes' => $data['notes'] ?? null]
                );
            }
            $session->update(['notes' => $request->notes]);

            // ── Sync ke student_attendances agar tampil di laporan ──
            $this->syncSessionToStudentAttendances($session);
        });

        return redirect()->route('teacher.sessions.index')
            ->with('success', 'Absensi berhasil diperbarui.');
    }

    // ── Private helpers ─────────────────────────────────────────────────────
    // resolveTeacher() disediakan oleh Trait ResolvesTeacher

    private function availableSubjects(?Teacher $teacher, $user)
    {
        if (!$teacher) {
            return Subject::where('is_active', true)->orderBy('class_group')->orderBy('name')->get();
        }
        if (in_array($user->role, ['wali_kelas', 'superadmin', 'admin'])) {
            return Subject::where('is_active', true)->orderBy('class_group')->orderBy('name')->get();
        }
        return $teacher->subjects()->where('is_active', true)->get();
    }
}
