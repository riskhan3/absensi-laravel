<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\{Student, StudentAttendance, Classroom};
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher  = Auth::user()->teacher; // relasi user->teacher
        $classroom = $teacher?->homeroomClass;

        if (!$classroom) {
            return view('teacher.no-class');
        }

        $today = Carbon::today()->toDateString();
        $total = Student::where('classroom_id', $classroom->id)->count();

        $stats = [
            'hadir' => StudentAttendance::where('date', $today)->where('classroom_id', $classroom->id)->where('status_id', 1)->count(),
            'sakit' => StudentAttendance::where('date', $today)->where('classroom_id', $classroom->id)->where('status_id', 2)->count(),
            'izin'  => StudentAttendance::where('date', $today)->where('classroom_id', $classroom->id)->where('status_id', 3)->count(),
            'alfa'  => max(0, $total - StudentAttendance::where('date', $today)->where('classroom_id', $classroom->id)->count()),
            'total' => $total,
        ];

        // Daftar siswa + status absen hari ini
        $students = Student::with(['todayAttendance.status'])
            ->where('classroom_id', $classroom->id)
            ->orderBy('name')
            ->get();

        // Data grafik 7 hari
        $chartData  = $this->getChartData($classroom->id, 7);
        $dateLabels = $this->getDateLabels(7);

        return view('teacher.dashboard', compact('classroom', 'stats', 'students', 'chartData', 'dateLabels'));
    }

    private function getChartData(int $classId, int $days): array
    {
        $data  = ['hadir' => [], 'sakit' => [], 'izin' => [], 'alfa' => []];
        $total = Student::where('classroom_id', $classId)->count();

        for ($i = $days - 1; $i >= 0; $i--) {
            $date  = Carbon::today()->subDays($i)->toDateString();
            $hadir = StudentAttendance::where('date', $date)->where('classroom_id', $classId)->where('status_id', 1)->count();
            $sakit = StudentAttendance::where('date', $date)->where('classroom_id', $classId)->where('status_id', 2)->count();
            $izin  = StudentAttendance::where('date', $date)->where('classroom_id', $classId)->where('status_id', 3)->count();
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
            $d = Carbon::today()->subDays($i);
            $labels[] = $i === 0 ? 'Hari ini' : $d->translatedFormat('D, d M');
        }
        return $labels;
    }
}
