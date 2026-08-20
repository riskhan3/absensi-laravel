@extends('layouts.app')
@section('title', 'Riwayat Absensi')
@section('page-title', 'Riwayat Absensi Kelas')

@section('content')
@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(()=>show=false,4000)"
     class="mb-5 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm flex items-center justify-between">
    <span>✅ {{ session('success') }}</span>
    <button @click="show=false">✕</button>
</div>
@endif

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-white font-bold text-xl">Riwayat Absensi</h2>
        <p class="text-slate-400 text-sm mt-0.5">{{ $sessions->total() }} sesi tersimpan</p>
    </div>
    <a href="{{ route('teacher.sessions.create') }}"
       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all shadow-lg shadow-blue-500/20">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Input Absensi Baru
    </a>
</div>

<div class="space-y-3">
    @forelse($sessions as $session)
    <div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-5 backdrop-blur hover:border-slate-600/60 transition-all">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                {{-- Kelas + Tanggal --}}
                <div class="flex items-center gap-3 mb-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-blue-500/10 border border-blue-500/20 text-blue-300 text-xs font-semibold">
                        {{ $session->classroom?->full_name ?? '—' }}
                    </span>
                    <span class="text-slate-400 text-sm">
                        {{ $session->date->translatedFormat('l, d F Y') }}
                    </span>
                </div>

                {{-- Mapel chips --}}
                <div class="flex flex-wrap gap-1.5 mb-2">
                    @foreach($session->subjects as $subj)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 text-xs font-medium">
                        {{ $subj->code }}
                    </span>
                    @endforeach
                </div>

                {{-- Stats kehadiran --}}
                @php
                    $counts = $session->studentAttendances->groupBy(fn($a) => $a->status->name ?? '?');
                    $total  = $session->studentAttendances->count();
                @endphp
                <div class="flex gap-3 text-xs">
                    <span class="text-emerald-400">✅ {{ $counts->get('Hadir', collect())->count() }} Hadir</span>
                    <span class="text-amber-400">🤒 {{ $counts->get('Sakit', collect())->count() }} Sakit</span>
                    <span class="text-blue-400">📋 {{ $counts->get('Izin', collect())->count() }} Izin</span>
                    <span class="text-red-400">❌ {{ $counts->get('Tanpa Keterangan', collect())->count() }} Alpha</span>
                    <span class="text-slate-500">/ {{ $total }} siswa</span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-2 flex-shrink-0">
                <a href="{{ route('teacher.sessions.show', $session) }}"
                   class="w-8 h-8 rounded-lg bg-slate-700/50 border border-slate-600/40 text-slate-400 hover:text-white flex items-center justify-center transition-all" title="Lihat Detail">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                </a>
                <a href="{{ route('teacher.sessions.edit', $session) }}"
                   class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 hover:bg-amber-500/20 flex items-center justify-center transition-all" title="Edit">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z"/></svg>
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-12 text-center">
        <svg class="w-12 h-12 mx-auto mb-3 text-slate-700" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z"/></svg>
        <p class="text-slate-400 font-medium">Belum ada absensi</p>
        <p class="text-slate-600 text-sm mt-1">Klik "Input Absensi Baru" untuk mulai.</p>
    </div>
    @endforelse
</div>

@if($sessions->hasPages())
<div class="mt-5">{{ $sessions->links() }}</div>
@endif
@endsection
