<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi — SD Negeri 30 Selayo</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Arial', sans-serif; font-size: 11px; color: #1e293b; background: #fff; }
        .page { max-width: 900px; margin: 0 auto; padding: 20px; }

        /* Header sekolah */
        .school-header { text-align: center; border-bottom: 3px solid #1e40af; padding-bottom: 12px; margin-bottom: 16px; }
        .school-header h1 { font-size: 16px; font-weight: bold; color: #1e40af; }
        .school-header p { font-size: 11px; color: #475569; }
        .report-title { text-align: center; margin-bottom: 16px; }
        .report-title h2 { font-size: 14px; font-weight: bold; text-transform: uppercase; }
        .report-title .period { font-size: 11px; color: #64748b; margin-top: 3px; }

        /* Summary boxes */
        .summary { display: flex; gap: 10px; margin-bottom: 16px; flex-wrap: wrap; }
        .summary-box { flex: 1; min-width: 100px; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; text-align: center; }
        .summary-box .num { font-size: 20px; font-weight: bold; }
        .summary-box .lbl { font-size: 10px; color: #64748b; }
        .summary-box.hadir { border-color: #10b981; background: #f0fdf4; }
        .summary-box.hadir .num { color: #059669; }
        .summary-box.sakit { border-color: #3b82f6; background: #eff6ff; }
        .summary-box.sakit .num { color: #2563eb; }
        .summary-box.izin  { border-color: #f59e0b; background: #fffbeb; }
        .summary-box.izin .num  { color: #d97706; }
        .summary-box.alfa  { border-color: #ef4444; background: #fef2f2; }
        .summary-box.alfa .num  { color: #dc2626; }

        /* Class section */
        .class-section { margin-bottom: 20px; page-break-inside: avoid; }
        .class-title { background: #1e40af; color: white; font-weight: bold; font-size: 12px; padding: 6px 12px; border-radius: 6px 6px 0 0; }

        /* Table */
        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        th { background: #dbeafe; color: #1e40af; padding: 6px 8px; text-align: left; font-weight: bold; border: 1px solid #bfdbfe; }
        td { padding: 5px 8px; border: 1px solid #e2e8f0; vertical-align: top; }
        tr:nth-child(even) td { background: #f8fafc; }

        /* Status badges */
        .badge { display: inline-block; padding: 1px 6px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .badge-hadir { background: #d1fae5; color: #065f46; }
        .badge-sakit { background: #dbeafe; color: #1d4ed8; }
        .badge-izin  { background: #fef3c7; color: #92400e; }
        .badge-alfa  { background: #fee2e2; color: #991b1b; }

        /* Footer */
        .print-footer { margin-top: 30px; display: flex; justify-content: space-between; }
        .signature-box { text-align: center; width: 200px; }
        .signature-box .line { border-top: 1px solid #1e293b; margin-top: 60px; padding-top: 4px; font-size: 10px; }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            .class-section { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
<div class="page">

    <!-- Print button (hidden on print) -->
    <div class="no-print" style="text-align:right; margin-bottom:12px;">
        <button onclick="window.print()" style="background:#1e40af;color:white;padding:8px 18px;border:none;border-radius:8px;cursor:pointer;font-size:12px;font-weight:bold;">🖨 Print / Simpan PDF</button>
        <button onclick="window.close()" style="background:#64748b;color:white;padding:8px 18px;border:none;border-radius:8px;cursor:pointer;font-size:12px;margin-left:8px;">✕ Tutup</button>
    </div>

    <!-- School Header -->
    <div class="school-header">
        <h1>SD NEGERI 30 SELAYO</h1>
        <p>Laporan Absensi {{ $type === 'student' ? 'Siswa' : ($type === 'teacher' ? 'Guru' : 'Staf') }}</p>
    </div>

    <!-- Report Title -->
    <div class="report-title">
        <h2>Laporan Absensi {{ $type === 'student' ? 'Siswa' : ($type === 'teacher' ? 'Guru' : 'Staf') }}</h2>
        <p class="period">Periode: {{ $data['period_label'] ?? '-' }}</p>
        <p class="period">Dicetak: {{ now()->translatedFormat('d F Y, H:i') }}</p>
    </div>

    <!-- Summary -->
    @if(isset($data['total']))
    <div class="summary">
        <div class="summary-box">
            <div class="num">{{ $data['total'] }}</div>
            <div class="lbl">Total</div>
        </div>
        @if($type === 'student')
        <div class="summary-box hadir">
            <div class="num">{{ $data['hadir'] }}</div>
            <div class="lbl">Hadir</div>
        </div>
        <div class="summary-box sakit">
            <div class="num">{{ $data['sakit'] }}</div>
            <div class="lbl">Sakit</div>
        </div>
        <div class="summary-box izin">
            <div class="num">{{ $data['izin'] }}</div>
            <div class="lbl">Izin</div>
        </div>
        <div class="summary-box alfa">
            <div class="num">{{ $data['alfa'] }}</div>
            <div class="lbl">Alpha</div>
        </div>
        @elseif(in_array($type, ['teacher','staff']))
        <div class="summary-box hadir">
            <div class="num">{{ $data['hadir'] ?? 0 }}</div>
            <div class="lbl">Hadir</div>
        </div>
        <div class="summary-box sakit">
            <div class="num">{{ $data['sakit'] ?? 0 }}</div>
            <div class="lbl">Sakit</div>
        </div>
        <div class="summary-box izin">
            <div class="num">{{ $data['izin'] ?? 0 }}</div>
            <div class="lbl">Izin</div>
        </div>
        <div class="summary-box alfa">
            <div class="num">{{ $data['alfa'] ?? 0 }}</div>
            <div class="lbl">Alpha</div>
        </div>
        @endif
    </div>

    {{-- ══ DATA SISWA ══ --}}
    @if($type === 'student' && isset($data['by_class']))
        @foreach($data['by_class'] as $className => $records)
        <div class="class-section">
            <div class="class-title">{{ $className }}</div>
            <table>
                <thead>
                    <tr>
                        <th style="width:30px">#</th>
                        <th>Tanggal</th>
                        <th>Nama Siswa</th>
                        <th>NIS</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Status</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $i => $rec)
                    @php
                        $statusMap  = [1=>'hadir',2=>'sakit',3=>'izin',4=>'alfa'];
                        $statusSlug = $statusMap[$rec->status_id ?? 1] ?? 'hadir';
                    @endphp
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($rec->date)->translatedFormat('d/m/Y') }}</td>
                        <td>{{ $rec->student?->name ?? '-' }}</td>
                        <td>{{ $rec->student?->nis ?? '-' }}</td>
                        <td>{{ $rec->time_in ?? '—' }}</td>
                        <td>{{ $rec->time_out ?? '—' }}</td>
                        <td><span class="badge badge-{{ $statusSlug }}">{{ $rec->status?->name ?? '-' }}</span></td>
                        <td>{{ $rec->subjects->pluck('name')->join(', ') ?: '—' }}</td>
                        <td>{{ $rec->teacher?->name ?? '—' }}</td>
                        <td>{{ $rec->notes ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endforeach

    {{-- ══ DATA GURU/STAF ══ --}}
    @elseif(in_array($type, ['teacher','staff']) && isset($data['by_person']))
        @foreach($data['by_person'] as $personName => $records)
        <div class="class-section">
            <div class="class-title" style="background:#065f46;">{{ $personName }}</div>
            <table>
                <thead>
                    <tr>
                        <th style="width:30px">#</th>
                        <th>Tanggal</th>
                        @if($type === 'teacher')
                        <th>NUPTK</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Status</th>
                        @else
                        <th>Jabatan</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        @endif
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $i => $rec)
                    @php
                        $statusMap2  = [1=>'hadir',2=>'sakit',3=>'izin',4=>'alfa'];
                        $statusSlug2 = $statusMap2[$rec->status_id ?? 1] ?? 'hadir';
                    @endphp
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($rec->date)->translatedFormat('d/m/Y') }}</td>
                        @if($type === 'teacher')
                        <td>{{ $rec->teacher?->nuptk ?? '-' }}</td>
                        <td>{{ $rec->time_in ?? '—' }}</td>
                        <td>{{ $rec->time_out ?? '—' }}</td>
                        <td><span class="badge badge-{{ $statusSlug2 }}">{{ $rec->status?->name ?? '-' }}</span></td>
                        @else
                        <td>{{ $rec->staff?->position ?? '-' }}</td>
                        <td>{{ $rec->check_in ?? '—' }}</td>
                        <td>{{ $rec->check_out ?? '—' }}</td>
                        @endif
                        <td>{{ $rec->notes ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endforeach
    @endif
    @endif

    <!-- Signature / TTD -->
    <div class="print-footer">
        <div class="signature-box">
            <div class="line">Kepala Sekolah</div>
        </div>
        <div class="signature-box">
            <div style="font-size:10px;color:#64748b;">Selayo, {{ now()->translatedFormat('d F Y') }}</div>
            @if($type === 'student' && !empty($data['teacher_names']))
            <div class="line">{{ implode(', ', $data['teacher_names']) }}</div>
            @else
            <div class="line">Petugas Absensi</div>
            @endif
        </div>
    </div>

</div>
</body>
</html>
