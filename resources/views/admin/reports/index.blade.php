@extends('layouts.app')

@section('title', 'Laporan Absensi')
@section('page-title', 'Laporan Absensi')

@section('content')
<div class="space-y-6">

{{-- ══ FILTER FORM ══ --}}
<form method="GET" action="{{ route('admin.reports.index') }}" id="filterForm">
<div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-6 backdrop-blur">
    <h3 class="text-white font-semibold mb-5 flex items-center gap-2">
        <svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75"/>
        </svg>
        Filter Laporan
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        {{-- Jenis --}}
        <div>
            <label class="block text-xs text-slate-400 mb-1.5 font-medium">Jenis Laporan</label>
            <select name="type" id="typeSelect" onchange="handleTypeChange(this.value)"
                    class="w-full bg-slate-700/60 border border-slate-600/50 text-slate-200 text-sm rounded-xl px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-blue-500/50">
                <option value="student" {{ $type==='student'?'selected':'' }}>🎒 Absensi Siswa</option>
                <option value="teacher" {{ $type==='teacher'?'selected':'' }}>👩‍🏫 Absensi Guru</option>
                <option value="staff"   {{ $type==='staff'?'selected':'' }}>👔 Absensi Staf</option>
            </select>
        </div>

        {{-- Periode (hanya untuk siswa) --}}
        <div id="periodWrap">
            <label class="block text-xs text-slate-400 mb-1.5 font-medium">Periode</label>
            <select name="period" id="periodSelect" onchange="handlePeriodChange(this.value)"
                    class="w-full bg-slate-700/60 border border-slate-600/50 text-slate-200 text-sm rounded-xl px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-blue-500/50">
                <option value="daily"   {{ $period==='daily'?'selected':'' }}>📅 Harian</option>
                <option value="weekly"  {{ $period==='weekly'?'selected':'' }}>📆 Mingguan</option>
                <option value="monthly" {{ $period==='monthly'?'selected':'' }}>🗓 Bulanan</option>
            </select>
        </div>

        {{-- Tanggal (harian/mingguan) --}}
        <div id="dateWrap" class="{{ in_array($period, ['daily','weekly']) && $type==='student' ? '' : 'hidden' }}">
            <label class="block text-xs text-slate-400 mb-1.5 font-medium">Tanggal</label>
            <input type="date" name="date" value="{{ $date }}"
                   class="w-full bg-slate-700/60 border border-slate-600/50 text-slate-200 text-sm rounded-xl px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-blue-500/50">
        </div>

        {{-- Bulan --}}
        <div id="monthWrap" class="{{ ($period==='monthly' || in_array($type, ['teacher','staff'])) ? '' : 'hidden' }}">
            <label class="block text-xs text-slate-400 mb-1.5 font-medium">Bulan</label>
            <input type="month" name="month" value="{{ $month }}"
                   class="w-full bg-slate-700/60 border border-slate-600/50 text-slate-200 text-sm rounded-xl px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-blue-500/50">
        </div>

        {{-- Filter Kelas (hanya siswa) --}}
        <div id="classWrap" class="{{ $type==='student' ? '' : 'hidden' }}">
            <label class="block text-xs text-slate-400 mb-1.5 font-medium">Filter Kelas</label>
            <select name="class_id"
                    class="w-full bg-slate-700/60 border border-slate-600/50 text-slate-200 text-sm rounded-xl px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-blue-500/50">
                <option value="">Semua Kelas</option>
                @foreach($classrooms as $kelas)
                <option value="{{ $kelas->id }}" {{ $classId == $kelas->id ? 'selected' : '' }}>Kelas {{ $kelas->grade_number }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-3 mt-5">
        <button type="submit" name="generate" value="1"
                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 15.803a7.5 7.5 0 0 0 10.607 0Z"/></svg>
            Tampilkan Laporan
        </button>
        @if(isset($data['total']))
        <a href="{{ route('admin.reports.download', request()->query()) }}" id="btn-download"
           class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
            Download Excel
        </a>
        <a href="{{ route('admin.reports.print', request()->query()) }}" id="btn-print" target="_blank"
           class="px-5 py-2.5 bg-slate-700 hover:bg-slate-600 text-white text-sm font-semibold rounded-xl transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/></svg>
            Print Laporan
        </a>
        @endif
    </div>
</div>
</form>

{{-- ══ STAT CARDS ══ --}}
@if(isset($data['total']) && $data['total'] > 0)
<div class="grid grid-cols-2 {{ $type === 'student' ? 'xl:grid-cols-4' : 'xl:grid-cols-3' }} gap-4">
    <div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-5 backdrop-blur">
        <div class="text-2xl mb-1">📊</div>
        <div class="text-3xl font-bold text-white mb-0.5">{{ $data['total'] }}</div>
        <div class="text-xs text-slate-400">Total Record</div>
    </div>
    @if($type === 'student')
    <div class="bg-emerald-500/10 border border-emerald-500/30 rounded-2xl p-5 backdrop-blur">
        <div class="text-2xl mb-1">✅</div>
        <div class="text-3xl font-bold text-emerald-300 mb-0.5">{{ $data['hadir'] }}</div>
        <div class="text-xs text-slate-400">Hadir</div>
    </div>
    <div class="bg-blue-500/10 border border-blue-500/30 rounded-2xl p-5 backdrop-blur">
        <div class="text-2xl mb-1">🩺</div>
        <div class="text-3xl font-bold text-blue-300 mb-0.5">{{ $data['sakit'] }}</div>
        <div class="text-xs text-slate-400">Sakit</div>
    </div>
    <div class="bg-amber-500/10 border border-amber-500/30 rounded-2xl p-5 backdrop-blur">
        <div class="text-2xl mb-1">📋</div>
        <div class="text-3xl font-bold text-amber-300 mb-0.5">{{ $data['izin'] }}</div>
        <div class="text-xs text-slate-400">Izin</div>
    </div>
    @elseif(in_array($type, ['teacher', 'staff']))
    <div class="bg-emerald-500/10 border border-emerald-500/30 rounded-2xl p-5 backdrop-blur">
        <div class="text-2xl mb-1">✅</div>
        <div class="text-3xl font-bold text-emerald-300 mb-0.5">{{ $data['hadir'] ?? 0 }}</div>
        <div class="text-xs text-slate-400">Hadir</div>
    </div>
    <div class="bg-red-500/10 border border-red-500/30 rounded-2xl p-5 backdrop-blur">
        <div class="text-2xl mb-1">❌</div>
        <div class="text-3xl font-bold text-red-300 mb-0.5">{{ $data['alfa'] ?? 0 }}</div>
        <div class="text-xs text-slate-400">Alpha</div>
    </div>
    @endif
</div>

{{-- ══ DATA TABLE ══ --}}
@if($type === 'student' && isset($data['by_class']))
    @foreach($data['by_class'] as $className => $records)
    <div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl overflow-hidden backdrop-blur">
        <div class="px-6 py-4 border-b border-slate-700/50 flex items-center justify-between">
            <h3 class="text-white font-semibold text-sm flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                {{ $className }}
            </h3>
            <span class="text-xs text-slate-400 bg-slate-700/50 px-2 py-0.5 rounded-full">{{ count($records) }} record</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-700/30">
                        <th class="px-4 py-3 text-left font-medium">#</th>
                        <th class="px-4 py-3 text-left font-medium">Tanggal</th>
                        <th class="px-4 py-3 text-left font-medium">Nama Siswa</th>
                        <th class="px-4 py-3 text-left font-medium">NIS</th>
                        <th class="px-4 py-3 text-center font-medium">Masuk</th>
                        <th class="px-4 py-3 text-center font-medium">Pulang</th>
                        <th class="px-4 py-3 text-center font-medium">Status</th>
                        <th class="px-4 py-3 text-left font-medium">Mata Pelajaran</th>
                        <th class="px-4 py-3 text-left font-medium">Guru</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/30">
                    @foreach($records as $i => $rec)
                    <tr class="hover:bg-slate-700/20 transition-colors">
                        <td class="px-4 py-3 text-slate-500">{{ $i+1 }}</td>
                        <td class="px-4 py-3 text-slate-300">{{ \Carbon\Carbon::parse($rec->date)->translatedFormat('d M Y') }}</td>
                        <td class="px-4 py-3 text-white font-medium">{{ $rec->student?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $rec->student?->nis ?? '-' }}</td>
                        <td class="px-4 py-3 text-center text-slate-300">{{ $rec->time_in ?? '—' }}</td>
                        <td class="px-4 py-3 text-center text-slate-300">{{ $rec->time_out ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $statusColors = [1=>'emerald',2=>'blue',3=>'amber',4=>'red'];
                                $statusId = $rec->status_id ?? 1;
                                $color = $statusColors[$statusId] ?? 'slate';
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-{{ $color }}-300 bg-{{ $color }}-500/10 border border-{{ $color }}-500/30">
                                {{ $rec->status?->name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-400">
                            {{ $rec->subjects->pluck('name')->join(', ') ?: '—' }}
                        </td>
                        <td class="px-4 py-3 text-slate-400">
                            {{ $rec->teacher?->name ?? '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach

    {{-- ── Info Guru yang Mengabsen ── --}}
    @if(!empty($data['teacher_names']))
    <div class="bg-slate-800/50 border border-blue-500/20 rounded-2xl p-5 backdrop-blur flex items-start gap-3">
        <div class="w-9 h-9 rounded-xl bg-blue-600/20 border border-blue-500/30 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
            </svg>
        </div>
        <div>
            <p class="text-blue-300 text-xs font-semibold mb-1">Guru yang Mengambil Absensi</p>
            <p class="text-white text-sm font-medium">{{ implode(', ', $data['teacher_names']) }}</p>
        </div>
    </div>
    @endif

@elseif(in_array($type, ['teacher','staff']) && isset($data['by_person']))
    @foreach($data['by_person'] as $personName => $records)
    <div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl overflow-hidden backdrop-blur">
        <div class="px-6 py-4 border-b border-slate-700/50 flex items-center justify-between">
            <h3 class="text-white font-semibold text-sm flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                {{ $personName }}
            </h3>
            <span class="text-xs text-slate-400 bg-slate-700/50 px-2 py-0.5 rounded-full">{{ count($records) }} hari</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-700/30">
                        <th class="px-4 py-3 text-left font-medium">#</th>
                        <th class="px-4 py-3 text-left font-medium">Tanggal</th>
                        @if($type === 'teacher')
                        <th class="px-4 py-3 text-left font-medium">NUPTK</th>
                        <th class="px-4 py-3 text-center font-medium">Masuk</th>
                        <th class="px-4 py-3 text-center font-medium">Pulang</th>
                        <th class="px-4 py-3 text-center font-medium">Status</th>
                        @else
                        <th class="px-4 py-3 text-left font-medium">Jabatan</th>
                        <th class="px-4 py-3 text-center font-medium">Masuk</th>
                        <th class="px-4 py-3 text-center font-medium">Pulang</th>
                        @endif
                        <th class="px-4 py-3 text-left font-medium">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/30">
                    @foreach($records as $i => $rec)
                    <tr class="hover:bg-slate-700/20 transition-colors">
                        <td class="px-4 py-3 text-slate-500">{{ $i+1 }}</td>
                        <td class="px-4 py-3 text-slate-300">{{ \Carbon\Carbon::parse($rec->date)->translatedFormat('d M Y') }}</td>
                        @if($type === 'teacher')
                        <td class="px-4 py-3 text-slate-400">{{ $rec->teacher?->nuptk ?? '-' }}</td>
                        <td class="px-4 py-3 text-center text-slate-300">{{ $rec->time_in ?? '—' }}</td>
                        <td class="px-4 py-3 text-center text-slate-300">{{ $rec->time_out ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            @php $statusColors2 = [1=>'emerald',2=>'blue',3=>'amber',4=>'red']; $sc = $statusColors2[$rec->status_id??1]??'slate'; @endphp
                            <span class="px-2 py-0.5 rounded-full text-{{ $sc }}-300 bg-{{ $sc }}-500/10 border border-{{ $sc }}-500/30">{{ $rec->status?->name ?? '-' }}</span>
                        </td>
                        @else
                        <td class="px-4 py-3 text-slate-400">{{ $rec->staff?->position ?? '-' }}</td>
                        <td class="px-4 py-3 text-center text-slate-300">{{ $rec->check_in ?? '—' }}</td>
                        <td class="px-4 py-3 text-center text-slate-300">{{ $rec->check_out ?? '—' }}</td>
                        @endif
                        <td class="px-4 py-3 text-slate-500">{{ $rec->notes ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
@endif

@elseif(request()->has('generate'))
<div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-12 text-center backdrop-blur">
    <div class="text-4xl mb-3">📭</div>
    <p class="text-slate-400">Tidak ada data untuk filter yang dipilih.</p>
</div>
@endif

</div>
@endsection

@push('scripts')
<script>
function handleTypeChange(type) {
    const isPeriod  = type === 'student';
    document.getElementById('periodWrap').classList.toggle('hidden', !isPeriod);
    document.getElementById('classWrap').classList.toggle('hidden', !isPeriod);
    // Guru & staf selalu bulanan
    if (!isPeriod) {
        document.getElementById('dateWrap').classList.add('hidden');
        document.getElementById('monthWrap').classList.remove('hidden');
    } else {
        handlePeriodChange(document.getElementById('periodSelect').value);
    }
}

function handlePeriodChange(period) {
    document.getElementById('dateWrap').classList.toggle('hidden', period === 'monthly');
    document.getElementById('monthWrap').classList.toggle('hidden', period !== 'monthly');
}
</script>
@endpush
