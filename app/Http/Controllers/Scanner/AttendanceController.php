<?php

namespace App\Http\Controllers\Scanner;

use App\Exceptions\AlreadyCheckedInException;
use App\Exceptions\GeofencingException;
use App\Exceptions\InvalidScanCodeException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ScanAttendanceRequest;
use App\Models\GeneralSetting;
use App\Models\Subject;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function __construct(private readonly AttendanceService $attendanceService) {}

    /** Halaman pemilihan scanner — menampilkan info guru yang login */
    public function index(): \Illuminate\View\View
    {
        $user    = Auth::user();
        $teacher = $user?->teacher;
        return view('scanner.index', compact('user', 'teacher'));
    }

    /** Scanner 1 — Absensi Siswa (dengan pilih mapel, guru dari login) */
    public function indexSiswa(): \Illuminate\View\View
    {
        $settings = GeneralSetting::instance();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $user     = Auth::user();
        $teacher  = $user?->teacher;
        return view('scanner.siswa', compact('settings', 'subjects', 'user', 'teacher'));
    }

    /** Scanner 2 — Absensi Guru & Staf (masuk/pulang) */
    public function indexGuruStaf(): \Illuminate\View\View
    {
        $settings = GeneralSetting::instance();
        $user     = Auth::user();
        return view('scanner.guru-staf', compact('settings', 'user'));
    }

    public function checkIn(ScanAttendanceRequest $request): JsonResponse
    {
        return $this->handle($request, 'in');
    }

    public function checkOut(ScanAttendanceRequest $request): JsonResponse
    {
        return $this->handle($request, 'out');
    }

    private function handle(ScanAttendanceRequest $request, string $direction): JsonResponse
    {
        try {
            $subjectIds = $request->validated('subject_ids') ?? [];

            // teacher_id otomatis dari guru yang sedang login
            $user      = Auth::user();
            $teacherId = $user?->teacher?->id;

            $result = $direction === 'in'
                ? $this->attendanceService->processCheckIn(
                    $request->validated('scan_code'),
                    (float) $request->validated('latitude'),
                    (float) $request->validated('longitude'),
                    $subjectIds,
                    $teacherId,
                  )
                : $this->attendanceService->processCheckOut(
                    $request->validated('scan_code'),
                    (float) $request->validated('latitude'),
                    (float) $request->validated('longitude'),
                  );

            $p = $result['person'];

            // Tambahkan info kelas, mapel & guru untuk siswa
            $classroomName = '';
            $subjectNames  = '';
            $teacherName   = '';
            if ($result['type'] === 'student') {
                $p->loadMissing('classroom');
                $classroomName = $p->classroom?->grade_label ?? '-';
                if (!empty($result['record'])) {
                    $result['record']->load(['subjects', 'teacher']);
                    $subjectNames = $result['record']->subjects->pluck('name')->join(', ');
                    $teacherName  = $result['record']->teacher?->name ?? '';
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Absensi berhasil!',
                'data'    => [
                    'name'      => $p->name,
                    'id_num'    => $result['type'] === 'student' ? $p->nis : ($result['type'] === 'staff' ? ($p->nip ?? '-') : $p->nuptk),
                    'type'      => $result['type'],
                    'time'      => now()->format('H:i:s'),
                    'classroom' => $classroomName,
                    'subjects'  => $subjectNames,
                    'teacher'   => $teacherName,
                ],
            ]);

        } catch (GeofencingException $e) {
            return response()->json(['success' => false, 'code' => 'GEOFENCE_VIOLATION', 'message' => $e->getMessage()], 403);
        } catch (AlreadyCheckedInException $e) {
            return response()->json(['success' => false, 'code' => 'ALREADY_CHECKED_IN', 'message' => $e->getMessage()], 409);
        } catch (InvalidScanCodeException $e) {
            return response()->json(['success' => false, 'code' => 'INVALID_CODE', 'message' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['success' => false, 'code' => 'SERVER_ERROR', 'message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }
}
