@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')
<!-- ══ STAT CARDS ══ -->
<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['label'=>'Hadir Siswa',  'value'=>$studentStats['hadir'], 'total'=>$studentStats['total'], 'color'=>'emerald', 'icon'=>'✓'],
        ['label'=>'Sakit',        'value'=>$studentStats['sakit'], 'total'=>$studentStats['total'], 'color'=>'blue',    'icon'=>'🩺'],
        ['label'=>'Izin',         'value'=>$studentStats['izin'],  'total'=>$studentStats['total'], 'color'=>'amber',   'icon'=>'📋'],
        ['label'=>'Alpha',        'value'=>$studentStats['alfa'],  'total'=>$studentStats['total'], 'color'=>'red',     'icon'=>'✗'],
    ] as $s)
    <div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-5 backdrop-blur hover:border-slate-600/70 transition-all">
        <div class="flex items-start justify-between mb-3">
            <div class="text-2xl">{{ $s['icon'] }}</div>
            <span class="text-xs text-slate-500 bg-slate-700/50 px-2 py-0.5 rounded-full">
                {{ $s['total'] > 0 ? round($s['value']/$s['total']*100) : 0 }}%
            </span>
        </div>
        <div class="text-3xl font-bold text-white mb-0.5">{{ $s['value'] }}</div>
        <div class="text-xs text-slate-400">{{ $s['label'] }} hari ini</div>
        <!-- Mini progress bar -->
        <div class="mt-3 h-1 bg-slate-700 rounded-full overflow-hidden">
            <div class="h-full bg-{{ $s['color'] }}-500 rounded-full transition-all duration-1000"
                 style="width: {{ $s['total'] > 0 ? round($s['value']/$s['total']*100) : 0 }}%"></div>
        </div>
    </div>
    @endforeach
</div>

<!-- ══ CHARTS + TEACHER STATS ══ -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">

    <!-- Attendance Chart (2/3 width) -->
    <div class="xl:col-span-2 bg-slate-800/50 border border-slate-700/50 rounded-2xl p-6 backdrop-blur">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-white font-semibold">Tren Kehadiran Siswa</h3>
                <p class="text-slate-400 text-xs mt-0.5">7 hari terakhir</p>
            </div>
            <!-- Filter Kelas -->
            <select id="classFilter" onchange="filterClass(this.value)"
                    class="bg-slate-700/60 border border-slate-600/50 text-slate-300 text-xs rounded-lg px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-blue-500/50">
                <option value="">Semua Kelas</option>
                @foreach($classrooms as $kelas)
                <option value="{{ $kelas->id }}">{{ $kelas->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div id="attendanceChart"></div>
    </div>

    <!-- Teacher Stats (1/3 width) -->
    <div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-6 backdrop-blur">
        <h3 class="text-white font-semibold mb-1">Kehadiran Guru</h3>
        <p class="text-slate-400 text-xs mb-5">{{ now()->translatedFormat('d F Y') }}</p>

        <div id="teacherDonut"></div>

        <div class="mt-4 space-y-2">
            @foreach([
                ['label'=>'Hadir', 'value'=>$teacherStats['hadir'], 'color'=>'bg-emerald-500'],
                ['label'=>'Sakit', 'value'=>$teacherStats['sakit'], 'color'=>'bg-blue-500'],
                ['label'=>'Izin',  'value'=>$teacherStats['izin'],  'color'=>'bg-amber-500'],
                ['label'=>'Alpha', 'value'=>$teacherStats['alfa'],  'color'=>'bg-red-500'],
            ] as $t)
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full {{ $t['color'] }}"></div>
                    <span class="text-slate-400 text-xs">{{ $t['label'] }}</span>
                </div>
                <span class="text-white font-semibold text-xs">{{ $t['value'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- ══ CLASS TABLE ══ -->
<div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-6 backdrop-blur">
    <h3 class="text-white font-semibold mb-4">Ringkasan Per Kelas</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-slate-400 text-xs uppercase tracking-wider border-b border-slate-700/50">
                    <th class="pb-3 text-left font-medium">Kelas</th>
                    <th class="pb-3 text-center font-medium">Total Siswa</th>
                    <th class="pb-3 text-center font-medium">Wali Kelas</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/30">
                @foreach($classrooms as $kelas)
                <tr class="hover:bg-slate-700/20 transition-colors">
                    <td class="py-3 text-white font-medium">{{ $kelas->full_name }}</td>
                    <td class="py-3 text-center text-slate-300">{{ $kelas->students_count }}</td>
                    <td class="py-3 text-center text-slate-300 text-xs">
                        {{ $kelas->homeroomTeacher?->name ?? '<span class="text-slate-600">—</span>' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── ApexCharts: Tren Kehadiran (Area Chart) ──────────────────────────────────
const chartEl = document.getElementById('attendanceChart');
const chartOpts = {
    chart: { type:'area', height:240, toolbar:{ show:true, tools:{ download:true } }, background:'transparent',
             animations:{ enabled:true, easing:'easeinout', speed:900 }, zoom:{ enabled:false } },
    theme: { mode:'dark' },
    colors: ['#10b981','#3b82f6','#f59e0b','#ef4444'],
    fill:   { type:'gradient', gradient:{ opacityFrom:0.35, opacityTo:0.03 } },
    stroke: { curve:'smooth', width:2 },
    grid:   { borderColor:'rgba(255,255,255,0.05)', strokeDashArray:4 },
    dataLabels: { enabled:false },
    xaxis: { categories: @json($dateLabels), labels:{ style:{ colors:'#94a3b8', fontSize:'11px' } }, axisBorder:{ show:false }, axisTicks:{ show:false } },
    yaxis: { labels:{ style:{ colors:'#94a3b8', fontSize:'11px' } }, min:0 },
    legend: { labels:{ colors:'#94a3b8' }, fontSize:'12px' },
    tooltip: { theme:'dark', shared:true, intersect:false },
    series: [
        { name:'Hadir', data: @json($chartData['hadir']) },
        { name:'Sakit', data: @json($chartData['sakit']) },
        { name:'Izin',  data: @json($chartData['izin'])  },
        { name:'Alpha', data: @json($chartData['alfa'])  },
    ],
};
const attendanceChart = new ApexCharts(chartEl, chartOpts);
attendanceChart.render();

// ── ApexCharts: Donut Guru ───────────────────────────────────────────────────
const teacherOpts = {
    chart: { type:'donut', height:160, background:'transparent' },
    theme: { mode:'dark' },
    colors: ['#10b981','#3b82f6','#f59e0b','#ef4444'],
    series: [{{ $teacherStats['hadir'] }}, {{ $teacherStats['sakit'] }}, {{ $teacherStats['izin'] }}, {{ $teacherStats['alfa'] }}],
    labels: ['Hadir','Sakit','Izin','Alpha'],
    dataLabels: { enabled:false },
    legend: { show:false },
    plotOptions: { pie: { donut: { size:'70%' } } },
    stroke: { show:false },
    tooltip: { theme:'dark' },
};
new ApexCharts(document.getElementById('teacherDonut'), teacherOpts).render();

// ── Filter by class (AJAX) ───────────────────────────────────────────────────
async function filterClass(classId) {
    const res  = await fetch('{{ route("admin.filter-class") }}', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
        body: JSON.stringify({ class_id: classId }),
    });
    const data = await res.json();
    attendanceChart.updateSeries([
        { name:'Hadir', data:[data.hadir] },
        { name:'Sakit', data:[data.sakit] },
        { name:'Izin',  data:[data.izin]  },
        { name:'Alpha', data:[data.alfa]  },
    ]);
}
</script>
@endpush
