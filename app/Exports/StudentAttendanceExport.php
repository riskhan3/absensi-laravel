<?php

namespace App\Exports;

use App\Models\Classroom;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentAttendanceExport implements WithMultipleSheets
{
    public function __construct(
        private string  $period,
        private string  $date,
        private string  $month,
        private ?string $classId,
    ) {}

    public function sheets(): array
    {
        [$start, $end] = $this->getDateRange();

        $query = StudentAttendance::with(['student.classroom.major', 'status', 'subjects'])
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->orderBy('student_id');

        if ($this->classId) {
            $query->where('classroom_id', $this->classId);
        }

        $records = $query->get();

        // Group by classroom
        $byClass = $records->groupBy(fn($r) => $r->student?->classroom?->full_name ?? 'Tidak Ada Kelas');

        $sheets = [];
        foreach ($byClass as $className => $classRecords) {
            $sheets[] = new StudentClassSheet($className, $classRecords, $start, $end);
        }

        if (empty($sheets)) {
            $sheets[] = new StudentClassSheet('Semua Kelas', collect(), $start, $end);
        }

        return $sheets;
    }

    private function getDateRange(): array
    {
        return match ($this->period) {
            'daily'  => [$this->date, $this->date],
            'weekly' => [
                Carbon::parse($this->date)->startOfWeek()->toDateString(),
                Carbon::parse($this->date)->endOfWeek()->toDateString(),
            ],
            default  => [
                Carbon::parse($this->month . '-01')->startOfMonth()->toDateString(),
                Carbon::parse($this->month . '-01')->endOfMonth()->toDateString(),
            ],
        };
    }
}

class StudentClassSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    /** Nomor urut per sheet — sebagai property instance agar reset di setiap sheet */
    private int $no = 0;

    public function __construct(
        private string     $className,
        private \Illuminate\Support\Collection $records,
        private string     $start,
        private string     $end,
    ) {}

    public function title(): string
    {
        return substr($this->className, 0, 31); // Excel sheet name max 31 chars
    }

    public function collection()
    {
        return $this->records;
    }

    public function headings(): array
    {
        return ['No', 'Tanggal', 'Nama Siswa', 'NIS', 'Kelas', 'Jam Masuk', 'Jam Pulang', 'Status', 'Mata Pelajaran', 'Catatan'];
    }

    public function map($row): array
    {
        // Nomor urut di-increment sebagai property instance (bukan static)
        // agar selalu mulai dari 1 di setiap sheet
        $this->no++;

        $subjects = $row->subjects->pluck('name')->join(', ');

        return [
            $this->no,
            Carbon::parse($row->date)->translatedFormat('d/m/Y'),
            $row->student?->name ?? '-',
            $row->student?->nis ?? '-',
            $row->student?->classroom?->full_name ?? '-',
            $row->time_in ?? '-',
            $row->time_out ?? '-',
            $row->status?->name ?? '-',
            $subjects ?: '-',
            $row->notes ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E40AF']],
            ],
        ];
    }
}
