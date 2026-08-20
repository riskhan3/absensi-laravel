<?php

namespace App\Exports;

use App\Models\TeacherAttendance;
use App\Models\StaffAttendance;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StaffTeacherAttendanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(
        private string $type,  // 'teacher' | 'staff'
        private string $month,
    ) {}

    public function title(): string
    {
        return $this->type === 'teacher' ? 'Absensi Guru' : 'Absensi Staf';
    }

    public function collection()
    {
        [$start, $end] = $this->getMonthRange();

        if ($this->type === 'teacher') {
            return TeacherAttendance::with(['teacher', 'status'])
                ->whereBetween('date', [$start, $end])
                ->orderBy('teacher_id')
                ->orderBy('date')
                ->get();
        }

        return StaffAttendance::with('staff')
            ->whereBetween('date', [$start, $end])
            ->orderBy('staff_id')
            ->orderBy('date')
            ->get();
    }

    public function headings(): array
    {
        if ($this->type === 'teacher') {
            return ['No', 'Tanggal', 'Nama Guru', 'NUPTK', 'Jam Masuk', 'Jam Pulang', 'Status', 'Catatan'];
        }
        return ['No', 'Tanggal', 'Nama Staf', 'NIP', 'Jabatan', 'Jam Masuk', 'Jam Pulang', 'Catatan'];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        if ($this->type === 'teacher') {
            return [
                $no,
                Carbon::parse($row->date)->translatedFormat('d/m/Y'),
                $row->teacher?->name ?? '-',
                $row->teacher?->nuptk ?? '-',
                $row->time_in ?? '-',
                $row->time_out ?? '-',
                $row->status?->name ?? '-',
                $row->notes ?? '-',
            ];
        }

        return [
            $no,
            Carbon::parse($row->date)->translatedFormat('d/m/Y'),
            $row->staff?->name ?? '-',
            $row->staff?->nip ?? '-',
            $row->staff?->position ?? '-',
            $row->check_in ?? '-',
            $row->check_out ?? '-',
            $row->notes ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '065F46']],
            ],
        ];
    }

    private function getMonthRange(): array
    {
        $d = Carbon::parse($this->month . '-01');
        return [$d->startOfMonth()->toDateString(), $d->copy()->endOfMonth()->toDateString()];
    }
}
