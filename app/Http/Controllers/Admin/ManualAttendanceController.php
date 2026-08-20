<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\AttendanceStatus;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManualAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $classrooms = Classroom::orderByRaw("FIELD(grade,'I','II','III','IV','V','VI')")->get();
        $statuses   = AttendanceStatus::all();
        $subjects   = Subject::where('is_active', true)->orderBy('name')->get();
        $date       = $request->input('date', today()->toDateString());
        $classId    = $request->input('class_id');

        $students = collect();
        if ($classId) {
            $students = Student::where('classroom_id', $classId)
                ->orderBy('name')
                ->with(['attendances' => fn($q) => $q->whereDate('date', $date)->with('subjects')])
                ->get();
        }

        // Deteksi konteks akses: scanner, teacher area, atau admin
        $routeName      = request()->route()->getName() ?? '';
        $isTeacherRoute = str_starts_with($routeName, 'scan.') || str_starts_with($routeName, 'teacher.');

        return view('admin.manual-attendance.index', compact(
            'classrooms', 'statuses', 'subjects', 'date', 'classId', 'students', 'isTeacherRoute'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'                       => ['required', 'date'],
            'class_id'                   => ['required', 'exists:classrooms,id'],
            'attendance'                 => ['required', 'array'],
            'attendance.*.student_id'    => ['required', 'exists:students,id'],
            'attendance.*.status_id'     => ['required', 'exists:attendance_statuses,id'],
            'attendance.*.notes'         => ['nullable', 'string', 'max:255'],
            'attendance.*.subject_ids'   => ['nullable', 'array'],
            'attendance.*.subject_ids.*' => ['integer', 'exists:subjects,id'],
        ]);

        // Ambil teacher_id dari user yang sedang login (null jika admin/tu)
        $teacherId     = Auth::user()->teacher_id ?? null;
        // Status default: cari id Hadir, fallback ke 1
        $defaultStatus = AttendanceStatus::where('name', 'Hadir')->value('id') ?? 1;

        DB::transaction(function () use ($request, $teacherId, $defaultStatus) {
            foreach ($request->input('attendance') as $item) {
                $attendance = StudentAttendance::updateOrCreate(
                    [
                        'student_id' => $item['student_id'],
                        'date'       => $request->input('date'),
                    ],
                    [
                        'classroom_id' => $request->input('class_id'),
                        'teacher_id'   => $teacherId,
                        'status_id'    => $item['status_id'] ?? $defaultStatus,
                        'notes'        => $item['notes'] ?? null,
                    ]
                );

                // Sync mata pelajaran ke pivot table
                $subjectIds = $item['subject_ids'] ?? [];
                $attendance->subjects()->sync($subjectIds);
            }
        });

        // Redirect ke route yang sesuai
        $routeName      = request()->route()->getName() ?? '';
        $isTeacher      = str_starts_with($routeName, 'scan.') || str_starts_with($routeName, 'teacher.');
        $isScan         = str_starts_with($routeName, 'scan.');

        $redirectRoute  = $isScan
            ? 'scan.absen-manual.index'
            : ($isTeacher ? 'teacher.manual-attendance.index' : 'admin.manual-attendance.index');

        return redirect()
            ->route($redirectRoute, [
                'date'     => $request->input('date'),
                'class_id' => $request->input('class_id'),
            ])
            ->with('success', 'Absensi manual berhasil disimpan.');
    }
}
