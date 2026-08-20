<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Classroom, GeneralSetting, Student, StudentAttendance, Teacher, TeacherAttendance, AttendanceStatus};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
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

    public function index()
    {
        $today    = Carbon::today()->toDateString();
        $settings = GeneralSetting::instance();
        $statuses = $this->getStatusIds();

        $hadirId = $statuses['Hadir'] ?? 1;
        $sakitId = $statuses['Sakit'] ?? 2;
        $izinId  = $statuses['Izin']  ?? 3;

        // Statistik siswa hari ini
        $studentStats = [
            'hadir' => StudentAttendance::where('date', $today)->where('status_id', $hadirId)->count(),
            'sakit' => StudentAttendance::where('date', $today)->where('status_id', $sakitId)->count(),
            'izin'  => StudentAttendance::where('date', $today)->where('status_id', $izinId)->count(),
            'alfa'  => Student::count() - StudentAttendance::where('date', $today)->count(),
            'total' => Student::count(),
        ];

        // Statistik guru hari ini
        $teacherStats = [
            'hadir' => TeacherAttendance::where('date', $today)->where('status_id', $hadirId)->count(),
            'sakit' => TeacherAttendance::where('date', $today)->where('status_id', $sakitId)->count(),
            'izin'  => TeacherAttendance::where('date', $today)->where('status_id', $izinId)->count(),
            'alfa'  => Teacher::count() - TeacherAttendance::where('date', $today)->count(),
            'total' => Teacher::count(),
        ];

        // Data grafik 7 hari terakhir (dioptimasi: 2 query saja)
        $chartData  = $this->getChartData(7, $hadirId, $sakitId, $izinId);
        $dateLabels = $this->getDateLabels(7);

        $classrooms = Classroom::with('major')->withCount('students')->get();

        return view('admin.dashboard', compact(
            'studentStats', 'teacherStats', 'chartData', 'dateLabels', 'classrooms', 'settings'
        ));
    }

    public function filterByClass()
    {
        $classId  = request('class_id');
        $today    = Carbon::today()->toDateString();
        $total    = Student::where('classroom_id', $classId)->count();
        $statuses = $this->getStatusIds();

        $hadirId = $statuses['Hadir'] ?? 1;
        $sakitId = $statuses['Sakit'] ?? 2;
        $izinId  = $statuses['Izin']  ?? 3;

        $stats = [
            'hadir' => StudentAttendance::where('date', $today)->where('classroom_id', $classId)->where('status_id', $hadirId)->count(),
            'sakit' => StudentAttendance::where('date', $today)->where('classroom_id', $classId)->where('status_id', $sakitId)->count(),
            'izin'  => StudentAttendance::where('date', $today)->where('classroom_id', $classId)->where('status_id', $izinId)->count(),
            'alfa'  => $total - StudentAttendance::where('date', $today)->where('classroom_id', $classId)->count(),
            'total' => $total,
        ];

        return response()->json($stats);
    }

    /**
     * Optimasi: dulu 21 query (3 per hari × 7 hari), sekarang 1 query groupBy.
     */
    private function getChartData(int $days, int $hadirId, int $sakitId, int $izinId): array
    {
        $startDate = Carbon::today()->subDays($days - 1)->toDateString();
        $endDate   = Carbon::today()->toDateString();
        $total     = Student::count();

        // Satu query: ambil semua data 7 hari, group by date + status_id
        $rows = StudentAttendance::whereBetween('date', [$startDate, $endDate])
            ->select('date', 'status_id', DB::raw('count(*) as total'))
            ->groupBy('date', 'status_id')
            ->get()
            ->groupBy(fn ($r) => $r->date->toDateString());

        $data = ['hadir' => [], 'sakit' => [], 'izin' => [], 'alfa' => []];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date    = Carbon::today()->subDays($i)->toDateString();
            $dayRows = $rows->get($date, collect());

            $hadir = (int) $dayRows->firstWhere('status_id', $hadirId)?->total;
            $sakit = (int) $dayRows->firstWhere('status_id', $sakitId)?->total;
            $izin  = (int) $dayRows->firstWhere('status_id', $izinId)?->total;

            $data['hadir'][] = $hadir;
            $data['sakit'][] = $sakit;
            $data['izin'][]  = $izin;
            $data['alfa'][]  = max(0, $total - $hadir - $sakit - $izin);
        }

        return $data;
    }

    private function getDateLabels(int $days): array
    {
        $labels = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $d        = Carbon::today()->subDays($i);
            $labels[] = $i === 0 ? 'Hari ini' : $d->translatedFormat('D, d M');
        }
        return $labels;
    }
}

