@extends('layouts.app')

@section('title', 'Dashboard Wali Kelas')
@section('page-title', 'Dashboard — ' . $classroom->full_name)

@section('content')
<!-- Header Kelas -->
<div class="bg-gradient-to-r from-blue-600/20 to-indigo-600/10 border border-blue-500/20 rounded-2xl p-5 mb-6 flex items-center gap-4">
    <div class="w-12 h-12 rounded-xl bg-blue-600/30 border border-blue-400/30 flex items-center justify-center flex-shrink-0">
        <svg class="w-7 h-7 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904"/>
        </svg>
    </div>
    <div>
        <h2 class="text-white font-bold text-lg">{{ $classroom->full_name }}</h2>
        <p class="text-blue-300/70 text-sm">{{ $stats['total'] }} siswa terdaftar · {{ now()->translatedFormat('d F Y') }}</p>
    </div>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['label'=>'Hadir', 'value'=>$stats['hadir'], 'color'=>'emerald', 'bg'=>'bg-emerald-500/10', 'border'=>'border-emerald-500/20'],
        ['label'=>'Sakit', 'value'=>$stats['sakit'], 'color'=>'blue',    'bg'=>'bg-blue-500/10',    'border'=>'border-blue-500/20'],
        ['label'=>'Izin',  'value'=>$stats['izin'],  'color'=>'amber',   'bg'=>'bg-amber-500/10',   'border'=>'border-amber-500/20'],
        ['label'=>'Alpha', 'value'=>$stats['alfa'],  'color'=>'red',     'bg'=>'bg-red-500/10',     'border'=>'border-red-500/20'],
    ] as $s)
    <div class="{{ $s['bg'] }} border {{ $s['border'] }} rounded-2xl p-4 text-center">
        <div class="text-3xl font-bold text-{{ $s['color'] }}-400">{{ $s['value'] }}</div>
        <div class="text-xs text-slate-400 mt-1">{{ $s['label'] }}</div>
    </div>
    @endforeach
</div>

<!-- Chart + Student List -->
<div class="grid grid-cols-1 xl:grid-cols-5 gap-6">

    <!-- ApexChart -->
    <div class="xl:col-span-2 bg-slate-800/50 border border-slate-700/50 rounded-2xl p-5 backdrop-blur">
        <h3 class="text-white font-semibold mb-1">Tren 7 Hari</h3>
        <p class="text-slate-400 text-xs mb-4">{{ $classroom->full_name }}</p>
        <div id="classChart"></div>
    </div>

    <!-- Student List -->
    <div class="xl:col-span-3 bg-slate-800/50 border border-slate-700/50 rounded-2xl p-5 backdrop-blur">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-white font-semibold">Daftar Siswa Hari Ini</h3>
            <span class="text-xs text-slate-500 bg-slate-700/50 px-2 py-1 rounded-full">{{ count($students) }} siswa</span>
        </div>
        <div class="space-y-2 max-h-80 overflow-y-auto pr-1">
            @foreach($students as $siswa)
            @php $att = $siswa->todayAttendance; @endphp
            <div class="flex items-center gap-3 py-2 px-3 rounded-xl hover:bg-slate-700/30 transition-colors">
                <div class="w-8 h-8 rounded-lg bg-slate-700 flex items-center justify-center text-slate-300 text-xs font-bold flex-shrink-0">
                    {{ $loop->iteration }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white text-sm font-medium truncate">{{ $siswa->name }}</p>
                    <p class="text-slate-500 text-xs">{{ $siswa->nis }}</p>
                </div>
                <div>
                    @if($att)
                        @php $statusColors = [1=>'emerald',2=>'blue',3=>'amber',4=>'red']; $c = $statusColors[$att->status_id] ?? 'slate'; @endphp
                        <span class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full bg-{{ $c }}-500/15 text-{{ $c }}-400 border border-{{ $c }}-500/20">
                            {{ $att->status->name ?? 'Hadir' }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full bg-slate-700/50 text-slate-500 border border-slate-600/30">
                            Belum Absen
                        </span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
new ApexCharts(document.getElementById('classChart'), {
    chart: { type:'bar', height:220, background:'transparent', toolbar:{ show:false }, stacked:true },
    theme: { mode:'dark' },
    colors: ['#10b981','#3b82f6','#f59e0b','#ef4444'],
    fill: { opacity:0.85 },
    grid: { borderColor:'rgba(255,255,255,0.05)' },
    dataLabels: { enabled:false },
    xaxis: { categories: @json($dateLabels), labels:{ style:{ colors:'#94a3b8', fontSize:'10px' } }, axisBorder:{ show:false }, axisTicks:{ show:false } },
    yaxis: { labels:{ style:{ colors:'#94a3b8', fontSize:'10px' } }, min:0 },
    legend: { labels:{ colors:'#94a3b8' }, fontSize:'11px', position:'top' },
    tooltip: { theme:'dark', shared:true, intersect:false },
    plotOptions: { bar:{ borderRadius:4, columnWidth:'60%' } },
    series: [
        { name:'Hadir', data: @json($chartData['hadir']) },
        { name:'Sakit', data: @json($chartData['sakit']) },
        { name:'Izin',  data: @json($chartData['izin'])  },
        { name:'Alpha', data: @json($chartData['alfa'])  },
    ],
}).render();
</script>
@endpush
