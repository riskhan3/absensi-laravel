@extends('layouts.app')
@section('title', 'Edit Absensi')
@section('page-title', 'Edit Sesi Absensi')

@section('content')
<div class="max-w-3xl" x-data="editAbsen()">
<form action="{{ route('teacher.sessions.update', $session) }}" method="POST">
@csrf @method('PUT')

{{-- Info sesi --}}
<div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-4 mb-5 flex gap-4 items-center">
    <div>
        <p class="text-white font-semibold">{{ $session->classroom?->full_name }}</p>
        <p class="text-slate-400 text-sm">{{ $session->date->translatedFormat('l, d F Y') }}</p>
    </div>
</div>

{{-- Mapel --}}
<div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-5 mb-4">
    <h3 class="text-white font-semibold mb-3">Mata Pelajaran</h3>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
        @foreach($subjects as $subject)
        <label class="cursor-pointer">
            <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}" class="sr-only peer"
                   {{ $session->subjects->contains($subject->id) ? 'checked' : '' }}>
            <div class="rounded-xl border-2 p-2.5 transition-all
                        border-slate-600/60 bg-slate-700/30
                        peer-checked:border-blue-500 peer-checked:bg-blue-500/10">
                <span class="text-xs font-bold text-slate-300">{{ $subject->code }}</span>
                <p class="text-white text-sm mt-0.5">{{ $subject->name }}</p>
            </div>
        </label>
        @endforeach
    </div>
</div>

{{-- Daftar Siswa --}}
<div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl overflow-hidden mb-4">
    <div class="px-5 py-3 border-b border-slate-700/50 flex items-center justify-between">
        <h3 class="text-white font-semibold text-sm">Kehadiran Siswa</h3>
        <div class="flex gap-1.5">
            @foreach($statuses as $status)
            <button type="button" onclick="setAll({{ $status->id }})"
                    class="text-xs px-2.5 py-1 rounded-lg border border-slate-600/40 text-slate-400 hover:text-white hover:border-slate-500 transition-all">
                Semua {{ $status->name }}
            </button>
            @endforeach
        </div>
    </div>
    @foreach($session->classroom->students as $i => $siswa)
    @php $existing = $session->studentAttendances->firstWhere('student_id', $siswa->id); @endphp
    <div class="flex items-center gap-4 px-5 py-3.5 {{ !$loop->last ? 'border-b border-slate-700/30' : '' }} hover:bg-slate-700/20">
        <span class="text-slate-500 text-sm w-6 text-right">{{ $i+1 }}</span>
        <div class="flex-1 min-w-0">
            <p class="text-white text-sm font-medium">{{ $siswa->name }}</p>
            <p class="text-slate-500 text-xs">{{ $siswa->nis }}</p>
        </div>
        <div class="flex gap-1.5">
            @foreach($statuses as $status)
            <label>
                <input type="radio" name="attendances[{{ $siswa->id }}][status_id]" value="{{ $status->id }}"
                       class="sr-only peer"
                       {{ ($existing?->attendance_status_id == $status->id || (!$existing && $status->name === 'Hadir')) ? 'checked' : '' }}>
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold border cursor-pointer transition-all
                             {{ match($status->name) {
                                 'Hadir'  => 'border-slate-600/40 text-slate-400 hover:border-emerald-500/50 hover:text-emerald-400 peer-checked:bg-emerald-500/15 peer-checked:border-emerald-500/50 peer-checked:text-emerald-300',
                                 'Sakit'  => 'border-slate-600/40 text-slate-400 hover:border-amber-500/50 hover:text-amber-400 peer-checked:bg-amber-500/15 peer-checked:border-amber-500/50 peer-checked:text-amber-300',
                                 'Izin'   => 'border-slate-600/40 text-slate-400 hover:border-blue-500/50 hover:text-blue-400 peer-checked:bg-blue-500/15 peer-checked:border-blue-500/50 peer-checked:text-blue-300',
                                 default  => 'border-slate-600/40 text-slate-400 hover:border-red-500/50 hover:text-red-400 peer-checked:bg-red-500/15 peer-checked:border-red-500/50 peer-checked:text-red-300',
                             } }}">{{ $status->name }}</span>
            </label>
            @endforeach
        </div>
    </div>
    @endforeach
</div>

<div class="mb-4">
    <label class="block text-sm text-slate-300 mb-1.5">Catatan</label>
    <textarea name="notes" rows="2" class="w-full bg-slate-800/50 border border-slate-700/50 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">{{ $session->notes }}</textarea>
</div>

<div class="flex gap-3">
    <button type="submit" class="bg-amber-600 hover:bg-amber-500 text-white font-bold px-7 py-2.5 rounded-xl text-sm transition-all shadow-lg shadow-amber-500/20">
        💾 Perbarui Absensi
    </button>
    <a href="{{ route('teacher.sessions.show', $session) }}" class="px-5 py-2.5 bg-slate-700/60 text-slate-300 rounded-xl text-sm hover:bg-slate-600/60 transition-all">Batal</a>
</div>
</form>
</div>

<script>
function setAll(statusId) {
    @foreach($session->classroom->students as $siswa)
    document.querySelectorAll('input[name="attendances[{{ $siswa->id }}][status_id]"]')
        .forEach(r => r.checked = r.value == statusId);
    @endforeach
}
</script>
@endsection
