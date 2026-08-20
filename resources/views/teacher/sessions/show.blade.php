@extends('layouts.app')
@section('title', 'Detail Absensi')
@section('page-title', 'Detail Sesi Absensi')

@section('content')
<div class="max-w-3xl">
{{-- Header info --}}
<div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-5 backdrop-blur mb-5">
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div>
            <p class="text-slate-400 text-xs mb-1">Kelas</p>
            <p class="text-white font-semibold">{{ $session->classroom?->full_name }}</p>
        </div>
        <div>
            <p class="text-slate-400 text-xs mb-1">Tanggal</p>
            <p class="text-white font-semibold">{{ $session->date->translatedFormat('d F Y') }}</p>
        </div>
        <div>
            <p class="text-slate-400 text-xs mb-1">Guru</p>
            <p class="text-white font-semibold">{{ $session->teacher?->name ?? '—' }}</p>
        </div>
        <div>
            <p class="text-slate-400 text-xs mb-1">Total Siswa</p>
            <p class="text-white font-semibold">{{ $session->studentAttendances->count() }}</p>
        </div>
    </div>
    <div class="mt-3 pt-3 border-t border-slate-700/50">
        <p class="text-slate-400 text-xs mb-1.5">Mata Pelajaran</p>
        <div class="flex flex-wrap gap-1.5">
            @foreach($session->subjects as $subj)
            <span class="px-2.5 py-1 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 text-xs font-semibold">
                {{ $subj->code }} — {{ $subj->name }}
            </span>
            @endforeach
        </div>
    </div>
</div>

{{-- Rekap --}}
@php
    $counts = $session->studentAttendances->groupBy(fn($a) => $a->status->name ?? '?');
    $hadir  = $counts->get('Hadir', collect())->count();
    $sakit  = $counts->get('Sakit', collect())->count();
    $izin   = $counts->get('Izin', collect())->count();
    $alpha  = $counts->get('Tanpa Keterangan', collect())->count();
    $total  = $session->studentAttendances->count();
@endphp
<div class="grid grid-cols-4 gap-3 mb-5">
    <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-3 text-center">
        <div class="text-2xl font-bold text-emerald-300">{{ $hadir }}</div>
        <div class="text-xs text-emerald-400 mt-0.5">Hadir</div>
    </div>
    <div class="bg-amber-500/10 border border-amber-500/20 rounded-xl p-3 text-center">
        <div class="text-2xl font-bold text-amber-300">{{ $sakit }}</div>
        <div class="text-xs text-amber-400 mt-0.5">Sakit</div>
    </div>
    <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-3 text-center">
        <div class="text-2xl font-bold text-blue-300">{{ $izin }}</div>
        <div class="text-xs text-blue-400 mt-0.5">Izin</div>
    </div>
    <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-3 text-center">
        <div class="text-2xl font-bold text-red-300">{{ $alpha }}</div>
        <div class="text-xs text-red-400 mt-0.5">Alpha</div>
    </div>
</div>

{{-- Daftar siswa --}}
<div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-slate-700/50 text-slate-400 text-xs uppercase">
                <th class="px-5 py-3 text-left">No</th>
                <th class="px-5 py-3 text-left">Nama Siswa</th>
                <th class="px-5 py-3 text-left">NIS</th>
                <th class="px-5 py-3 text-center">Status</th>
                <th class="px-5 py-3 text-left">Catatan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-700/30">
            @foreach($session->studentAttendances as $i => $a)
            <tr class="hover:bg-slate-700/20 transition-colors">
                <td class="px-5 py-3 text-slate-500">{{ $i+1 }}</td>
                <td class="px-5 py-3 text-white font-medium">{{ $a->student?->name }}</td>
                <td class="px-5 py-3 text-slate-400 font-mono text-xs">{{ $a->student?->nis }}</td>
                <td class="px-5 py-3 text-center">
                    @php $sn = $a->status?->name ?? '?' @endphp
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold
                        {{ match($sn) {
                            'Hadir'            => 'bg-emerald-500/10 border border-emerald-500/20 text-emerald-300',
                            'Sakit'            => 'bg-amber-500/10 border border-amber-500/20 text-amber-300',
                            'Izin'             => 'bg-blue-500/10 border border-blue-500/20 text-blue-300',
                            default            => 'bg-red-500/10 border border-red-500/20 text-red-300',
                        } }}">
                        {{ $sn }}
                    </span>
                </td>
                <td class="px-5 py-3 text-slate-400 text-xs">{{ $a->notes ?: '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="flex gap-3 mt-5">
    <a href="{{ route('teacher.sessions.edit', $session) }}"
       class="bg-amber-600 hover:bg-amber-500 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-all">
        ✏️ Edit Absensi
    </a>
    <a href="{{ route('teacher.sessions.index') }}"
       class="bg-slate-700/60 hover:bg-slate-600/60 text-slate-300 px-5 py-2.5 rounded-xl text-sm transition-all">
        ← Kembali
    </a>
</div>
</div>
@endsection
