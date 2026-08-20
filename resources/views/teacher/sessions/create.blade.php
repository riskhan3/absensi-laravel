@extends('layouts.app')
@section('title', 'Input Absensi Kelas')
@section('page-title', 'Input Absensi Kelas')

@section('content')
<div x-data="absenForm()" x-init="init()">

{{-- STEP INDICATOR --}}
<div class="flex items-center gap-2 mb-6">
    <div class="flex items-center gap-2 text-sm">
        <span class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs transition-all"
              :class="step>=1 ? 'bg-blue-600 text-white' : 'bg-slate-700 text-slate-400'">1</span>
        <span :class="step>=1 ? 'text-white' : 'text-slate-500'" class="font-medium">Pilih Kelas & Tanggal</span>
    </div>
    <div class="flex-1 h-px bg-slate-700 mx-2"></div>
    <div class="flex items-center gap-2 text-sm">
        <span class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs transition-all"
              :class="step>=2 ? 'bg-blue-600 text-white' : 'bg-slate-700 text-slate-400'">2</span>
        <span :class="step>=2 ? 'text-white' : 'text-slate-500'" class="font-medium">Pilih Mata Pelajaran</span>
    </div>
    <div class="flex-1 h-px bg-slate-700 mx-2"></div>
    <div class="flex items-center gap-2 text-sm">
        <span class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs transition-all"
              :class="step>=3 ? 'bg-blue-600 text-white' : 'bg-slate-700 text-slate-400'">3</span>
        <span :class="step>=3 ? 'text-white' : 'text-slate-500'" class="font-medium">Isi Kehadiran</span>
    </div>
</div>

<form id="absenForm" action="{{ route('teacher.sessions.store') }}" method="POST">
@csrf

{{-- ══ STEP 1: Kelas & Tanggal ══════════════════════════════════════════ --}}
<div x-show="step===1" x-transition>
<div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-6 backdrop-blur mb-4">
    <h3 class="text-white font-semibold mb-4 flex items-center gap-2">
        <span class="w-6 h-6 bg-blue-600 rounded-full text-xs flex items-center justify-center">1</span>
        Pilih Kelas & Tanggal
    </h3>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1.5">Kelas <span class="text-red-400">*</span></label>
            <select name="classroom_id" x-model="classroomId" required
                    class="w-full bg-slate-700/50 border border-slate-600/60 text-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                <option value="">-- Pilih Kelas --</option>
                @foreach($classrooms as $kelas)
                <option value="{{ $kelas->id }}" data-grade="{{ $kelas->grade }}">{{ $kelas->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1.5">Tanggal <span class="text-red-400">*</span></label>
            <input type="date" name="date" x-model="date" required :max="today"
                   class="w-full bg-slate-700/50 border border-slate-600/60 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
        </div>
    </div>
</div>
<div class="flex justify-end">
    <button type="button" @click="nextStep()" :disabled="!classroomId || !date"
            class="bg-blue-600 hover:bg-blue-500 disabled:opacity-40 text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition-all">
        Lanjut → Pilih Mapel
    </button>
</div>
</div>

{{-- ══ STEP 2: Pilih Mata Pelajaran ═════════════════════════════════════ --}}
<div x-show="step===2" x-transition>
<div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-6 backdrop-blur mb-4">
    <h3 class="text-white font-semibold mb-1 flex items-center gap-2">
        <span class="w-6 h-6 bg-blue-600 rounded-full text-xs flex items-center justify-center">2</span>
        Pilih Mata Pelajaran
    </h3>
    <p class="text-slate-400 text-xs mb-4">Pilih satu atau lebih mapel yang diajarkan hari ini.</p>

    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
        @foreach($subjects as $subject)
        <label class="relative cursor-pointer group">
            <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}"
                   x-model="selectedSubjects" class="sr-only peer">
            <div class="rounded-xl border-2 p-3 transition-all duration-200
                        border-slate-600/60 bg-slate-700/30
                        peer-checked:border-blue-500 peer-checked:bg-blue-500/10">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-bold px-1.5 py-0.5 rounded
                                 {{ $subject->class_group === 'all' ? 'bg-slate-600/60 text-slate-300' : 'bg-indigo-500/20 text-indigo-300' }}">
                        {{ $subject->code }}
                    </span>
                    <svg class="w-4 h-4 text-blue-400 hidden peer-checked:block" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <p class="text-white text-sm font-medium leading-tight">{{ $subject->name }}</p>
                <p class="text-slate-500 text-xs mt-0.5">{{ $subject->class_group_label }}</p>
            </div>
        </label>
        @endforeach
    </div>

    <div class="mt-3 text-xs text-slate-500">
        <span x-text="selectedSubjects.length"></span> mapel dipilih
    </div>
</div>
<div class="flex gap-3 justify-between">
    <button type="button" @click="step=1" class="px-5 py-2.5 bg-slate-700/60 text-slate-300 rounded-xl text-sm hover:bg-slate-600/60 transition-all">← Kembali</button>
    <button type="button" @click="nextStep()" :disabled="selectedSubjects.length===0"
            class="bg-blue-600 hover:bg-blue-500 disabled:opacity-40 text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition-all">
        Lanjut → Isi Kehadiran
    </button>
</div>
</div>

{{-- ══ STEP 3: Isi Kehadiran ════════════════════════════════════════════ --}}
<div x-show="step===3" x-transition>

{{-- Toolbar: Tandai Semua --}}
<div class="flex items-center justify-between mb-3">
    <div>
        <h3 class="text-white font-semibold">Daftar Siswa</h3>
        <p class="text-slate-400 text-xs">Klik nama atau status untuk mengubah kehadiran.</p>
    </div>
    <div class="flex gap-2">
        @foreach($statuses as $status)
        <button type="button" @click="setAll({{ $status->id }})"
                class="text-xs px-3 py-1.5 rounded-lg border transition-all
                       {{ match($status->name) {
                           'Hadir'              => 'border-emerald-500/30 text-emerald-400 hover:bg-emerald-500/10',
                           'Sakit'              => 'border-amber-500/30 text-amber-400 hover:bg-amber-500/10',
                           'Izin'              => 'border-blue-500/30 text-blue-400 hover:bg-blue-500/10',
                           default             => 'border-red-500/30 text-red-400 hover:bg-red-500/10',
                       } }}">
            Semua {{ $status->name }}
        </button>
        @endforeach
    </div>
</div>

<div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl backdrop-blur overflow-hidden mb-4">
    @foreach($classrooms as $kelas)
    <div x-show="classroomId==={{ $kelas->id }}">
        @forelse($kelas->students as $i => $siswa)
        <div class="flex items-center gap-4 px-5 py-3.5 {{ !$loop->last ? 'border-b border-slate-700/30' : '' }} hover:bg-slate-700/20 transition-colors">
            {{-- No + Nama --}}
            <span class="text-slate-500 text-sm w-6 text-right flex-shrink-0">{{ $i+1 }}</span>
            <div class="flex-1 min-w-0">
                <p class="text-white text-sm font-medium truncate">{{ $siswa->name }}</p>
                <p class="text-slate-500 text-xs">NIS: {{ $siswa->nis }}</p>
            </div>
            {{-- Status Buttons --}}
            <div class="flex gap-1.5 flex-shrink-0">
                @foreach($statuses as $status)
                <label class="cursor-pointer">
                    <input type="radio"
                           name="attendances[{{ $siswa->id }}][status_id]"
                           value="{{ $status->id }}"
                           x-model="attendance[{{ $siswa->id }}]"
                           class="sr-only peer"
                           {{ $status->name === 'Hadir' ? 'checked' : '' }}>
                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg text-xs font-semibold border transition-all
                                 peer-checked:shadow-sm
                                 {{ match($status->name) {
                                     'Hadir'           => 'border-slate-600/40 text-slate-400 hover:border-emerald-500/50 hover:text-emerald-400 peer-checked:bg-emerald-500/15 peer-checked:border-emerald-500/50 peer-checked:text-emerald-300',
                                     'Sakit'           => 'border-slate-600/40 text-slate-400 hover:border-amber-500/50 hover:text-amber-400 peer-checked:bg-amber-500/15 peer-checked:border-amber-500/50 peer-checked:text-amber-300',
                                     'Izin'            => 'border-slate-600/40 text-slate-400 hover:border-blue-500/50 hover:text-blue-400 peer-checked:bg-blue-500/15 peer-checked:border-blue-500/50 peer-checked:text-blue-300',
                                     default           => 'border-slate-600/40 text-slate-400 hover:border-red-500/50 hover:text-red-400 peer-checked:bg-red-500/15 peer-checked:border-red-500/50 peer-checked:text-red-300',
                                 } }}">
                        {{ $status->name }}
                    </span>
                </label>
                @endforeach
            </div>
        </div>
        @empty
        <div class="px-5 py-8 text-center text-slate-500 text-sm">Tidak ada siswa di kelas ini.</div>
        @endforelse
    </div>
    @endforeach
</div>

{{-- Catatan --}}
<div class="mb-4">
    <label class="block text-sm font-medium text-slate-300 mb-1.5">Catatan (opsional)</label>
    <textarea name="notes" rows="2"
              class="w-full bg-slate-800/50 border border-slate-700/50 text-white placeholder-slate-500 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50"
              placeholder="Keterangan tambahan..."></textarea>
</div>

<div class="flex gap-3 justify-between">
    <button type="button" @click="step=2" class="px-5 py-2.5 bg-slate-700/60 text-slate-300 rounded-xl text-sm hover:bg-slate-600/60 transition-all">← Kembali</button>
    <button type="submit"
            class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-8 py-2.5 rounded-xl text-sm transition-all shadow-lg shadow-emerald-500/20">
        💾 Simpan Absensi
    </button>
</div>

</div>{{-- end step 3 --}}

</form>
</div>{{-- end x-data --}}
@endsection

@push('scripts')
<script>
function absenForm() {
    return {
        step: 1,
        classroomId: '',
        date: '{{ $today }}',
        today: '{{ $today }}',
        selectedSubjects: [],
        attendance: {},

        init() {
            // Default semua siswa = Hadir (status_id pertama dari Hadir)
            const haidirId = {{ $statuses->firstWhere('name','Hadir')?->id ?? 1 }};
            @foreach($classrooms as $kelas)
            @foreach($kelas->students as $siswa)
            this.attendance[{{ $siswa->id }}] = String(haidirId);
            @endforeach
            @endforeach
        },

        nextStep() {
            this.step++;
        },

        setAll(statusId) {
            const currentClassroomId = String(this.classroomId);
            @foreach($classrooms as $kelas)
            if (currentClassroomId === '{{ $kelas->id }}') {
                @foreach($kelas->students as $siswa)
                this.attendance[{{ $siswa->id }}] = String(statusId);
                // Update radio buttons
                const radios = document.querySelectorAll('input[name="attendances[{{ $siswa->id }}][status_id]"]');
                radios.forEach(r => r.checked = r.value === String(statusId));
                @endforeach
            }
            @endforeach
        }
    }
}
</script>
@endpush
