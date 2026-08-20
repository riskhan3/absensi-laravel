<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceStatus;
use App\Models\Classroom;
use App\Models\StudentAttendance;
use App\Models\TeacherAttendance;
use App\Models\StaffAttendance;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Staff;
use App\Exports\StudentAttendanceExport;
use App\Exports\StaffTeacherAttendanceExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceReportController extends Controller
{
    /** Cache status ID agar tidak query berulang dalam satu request */
    private function getStatusIds(): array
    {
        static $ids = null;
        if ($ids === null) {
            $ids = AttendanceStatus::pluck('id', 'name')->toArray();
        }
        return $ids;
    }

    /** Halaman laporan & filter */
    public function index(Request $request)
    {
        $classrooms = Classroom::with('major')->orderBy('grade')->orderBy('label')->get();

        // Defaults
        $type     = $request->input('type', 'student');       // student | teacher | staff
        $period   = $request->input('period', 'monthly');     // daily | weekly | monthly
        $date     = $request->input('date', today()->toDateString());
        $classId  = $request->input('class_id');
        $month    = $request->input('month', today()->format('Y-m'));

        $data = [];

        if ($request->filled('generate')) {
            if ($type === 'student') {
                $data = $this->getStudentData($period, $date, $month, $classId);
            } elseif ($type === 'teacher') {
                $data = $this->getTeacherData($month);
            } else {
                $data = $this->getStaffData($month);
            }
        }

        return view('admin.reports.index', compact(
            'classrooms', 'type', 'period', 'date', 'classId', 'month', 'data'
        ));
    }

    /** Download Excel */
    public function download(Request $request)
    {
        $type    = $request->input('type', 'student');
        $period  = $request->input('period', 'monthly');
        $date    = $request->input('date', today()->toDateString());
        $classId = $request->input('class_id');
        $month   = $request->input('month', today()->format('Y-m'));

        $filename = 'laporan-absensi-' . $type . '-' . now()->format('Ymd-His') . '.xlsx';

        if ($type === 'student') {
            return Excel::download(
                new StudentAttendanceExport($period, $date, $month, $classId),
                $filename
            );
        }

        return Excel::download(
            new StaffTeacherAttendanceExport($type, $month),
            $filename
        );
    }

    /** Halaman print */
    public function print(Request $request)
    {
        $classrooms = Classroom::with('major')->orderBy('grade')->orderBy('label')->get();
        $type    = $request->input('type', 'student');
        $period  = $request->input('period', 'monthly');
        $date    = $request->input('date', today()->toDateString());
        $classId = $request->input('class_id');
        $month   = $request->input('month', today()->format('Y-m'));

        if ($type === 'student') {
            $data = $this->getStudentData($period, $date, $month, $classId);
        } elseif ($type === 'teacher') {
            $data = $this->getTeacherData($month);
        } else {
            $data = $this->getStaffData($month);
        }

        return view('admin.reports.print', compact('type', 'period', 'date', 'classId', 'month', 'data', 'classrooms'));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ──────────────────────────────────────────────────────────────────────────

    private function getStudentData(string $period, string $date, string $month, ?string $classId): array
    {
        [$start, $end] = $this->getDateRange($period, $date, $month);
        $statuses = $this->getStatusIds();

        $hadirId = $statuses['Hadir'] ?? 1;
        $sakitId = $statuses['Sakit'] ?? 2;
        $izinId  = $statuses['Izin']  ?? 3;
        $alfaId  = $statuses['Tanpa Keterangan'] ?? $statuses['Alfa'] ?? 4;

        $query = StudentAttendance::with(['student.classroom.major', 'status', 'subjects', 'teacher'])
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->orderBy('student_id');

        if ($classId) {
            $query->where('classroom_id', $classId);
        }

        $records = $query->get();

        $byClass = [];
        foreach ($records as $rec) {
            $classroom = $rec->student?->classroom;
            $className = $classroom ? $classroom->grade_label : 'Tidak Ada Kelas';
            $byClass[$className][] = $rec;
        }

        // Kumpulkan nama guru yang mengambil absen (unik, tanpa null)
        $teacherNames = $records
            ->pluck('teacher.name')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        return [
            'period_label'  => $this->getPeriodLabel($period, $date, $month),
            'start'         => $start,
            'end'           => $end,
            'by_class'      => $byClass,
            'total'         => $records->count(),
            'hadir'         => $records->where('status_id', $hadirId)->count(),
            'sakit'         => $records->where('status_id', $sakitId)->count(),
            'izin'          => $records->where('status_id', $izinId)->count(),
            'alfa'          => $records->where('status_id', $alfaId)->count(),
            'teacher_names' => $teacherNames,
        ];
    }

    private function getTeacherData(string $month): array
    {
        [$start, $end] = $this->getMonthRange($month);
        $statuses = $this->getStatusIds();

        $hadirId = $statuses['Hadir'] ?? 1;
        $sakitId = $statuses['Sakit'] ?? 2;
        $izinId  = $statuses['Izin']  ?? 3;

        $records = TeacherAttendance::with(['teacher', 'status'])
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->get();

        $byTeacher = [];
        foreach ($records as $rec) {
            $name = $rec->teacher?->name ?? 'Unknown';
            $byTeacher[$name][] = $rec;
        }

        return [
            'period_label' => Carbon::parse($start)->translatedFormat('F Y'),
            'start'        => $start,
            'end'          => $end,
            'by_person'    => $byTeacher,
            'total'        => $records->count(),
            'hadir'        => $records->where('status_id', $hadirId)->count(),
            'sakit'        => $records->where('status_id', $sakitId)->count(),
            'izin'         => $records->where('status_id', $izinId)->count(),
            'alfa'         => $records->whereNotIn('status_id', [$hadirId, $sakitId, $izinId])->count(),
        ];
    }

    private function getStaffData(string $month): array
    {
        [$start, $end] = $this->getMonthRange($month);

        $records = StaffAttendance::with('staff')
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->get();

        $byStaff = [];
        foreach ($records as $rec) {
            $name = $rec->staff?->name ?? 'Unknown';
            $byStaff[$name][] = $rec;
        }

        // Hitung hadir (check_in tidak null) dan pulang (check_out tidak null)
        $hadir  = $records->whereNotNull('check_in')->count();
        $pulang = $records->whereNotNull('check_out')->count();

        return [
            'period_label' => Carbon::parse($start)->translatedFormat('F Y'),
            'start'        => $start,
            'end'          => $end,
            'by_person'    => $byStaff,
            'total'        => $records->count(),
            'hadir'        => $hadir,
            'pulang'       => $pulang,
        ];
    }

    private function getDateRange(string $period, string $date, string $month): array
    {
        return match ($period) {
            'daily'   => [$date, $date],
            'weekly'  => [
                Carbon::parse($date)->startOfWeek()->toDateString(),
                Carbon::parse($date)->endOfWeek()->toDateString(),
            ],
            default   => $this->getMonthRange($month),
        };
    }

    private function getMonthRange(string $month): array
    {
        $d = Carbon::parse($month . '-01');
        return [$d->startOfMonth()->toDateString(), $d->copy()->endOfMonth()->toDateString()];
    }

    private function getPeriodLabel(string $period, string $date, string $month): string
    {
        return match ($period) {
            'daily'  => Carbon::parse($date)->translatedFormat('l, d F Y'),
            'weekly' => 'Minggu ' . Carbon::parse($date)->startOfWeek()->translatedFormat('d F') . ' – ' . Carbon::parse($date)->endOfWeek()->translatedFormat('d F Y'),
            default  => Carbon::parse($month . '-01')->translatedFormat('F Y'),
        };
    }
}
