@extends('layouts.app')

@section('title', 'Absen Manual Siswa')
@section('page-title', 'Input Absen Manual Siswa')

@php
    $isTeacherRoute = $isTeacherRoute ?? false;
    $currentRoute   = request()->route()?->getName() ?? '';
    $isScanRoute    = str_starts_with($currentRoute, 'scan.');
    $isTeacherArea  = str_starts_with($currentRoute, 'teacher.') || ($isTeacherRoute && !$isScanRoute);

    $filterAction = $isScanRoute
        ? route('scan.absen-manual.index')
        : ($isTeacherArea ? route('teacher.manual-attendance.index') : route('admin.manual-attendance.index'));

    $storeAction  = $isScanRoute
        ? route('scan.absen-manual.store')
        : ($isTeacherArea ? route('teacher.manual-attendance.store') : route('admin.manual-attendance.store'));

    $backRoute    = $isScanRoute
        ? route('scan.index')
        : ($isTeacherArea ? route('teacher.sessions.index') : route('admin.dashboard'));
@endphp

@section('content')
<div class="space-y-6">

{{-- Tombol kembali ke scanner (hanya untuk guru) --}}
@if($isTeacherRoute)
<div class="flex items-center gap-3">
    <a href="{{ $backRoute }}"
       class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
        </svg>
        Kembali ke Scanner
    </a>
    <span class="text-slate-700">·</span>
    <span class="text-slate-500 text-sm">Absen Manual Siswa</span>
</div>
@endif

{{-- ══ FLASH SUCCESS ══ --}}
@if(session('success'))
<div class="flex items-center gap-3 px-5 py-3.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
    {{ session('success') }}
</div>
@endif

{{-- ══ FLASH ERROR / VALIDASI ══ --}}
@if($errors->any())
<div class="px-5 py-4 rounded-2xl bg-red-500/10 border border-red-500/30 text-red-300 text-sm">
    <div class="flex items-center gap-2 font-semibold mb-2">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
        Absensi gagal disimpan — ada kesalahan:
    </div>
    <ul class="list-disc list-inside space-y-0.5 text-xs text-red-200">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- ══ FILTER FORM ══ --}}
<form method="GET" action="{{ $filterAction }}"
      class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-6 backdrop-blur">
    <h3 class="text-white font-semibold mb-5 flex items-center gap-2">
        <svg class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z"/>
        </svg>
        Input Absen Manual
        <span class="ml-2 text-xs text-slate-400 font-normal">— untuk siswa sakit, izin, atau alfa</span>
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs text-slate-400 mb-1.5 font-medium">Kelas <span class="text-red-400">*</span></label>
            <select name="class_id" required
                    class="w-full bg-slate-700/60 border border-slate-600/50 text-slate-200 text-sm rounded-xl px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-amber-500/50">
                <option value="">-- Pilih Kelas --</option>
                @foreach($classrooms as $kelas)
                <option value="{{ $kelas->id }}" {{ $classId == $kelas->id ? 'selected' : '' }}>Kelas {{ $kelas->grade_number }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-slate-400 mb-1.5 font-medium">Tanggal <span class="text-red-400">*</span></label>
            <input type="date" name="date" value="{{ $date }}" required
                   class="w-full bg-slate-700/60 border border-slate-600/50 text-slate-200 text-sm rounded-xl px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-amber-500/50">
        </div>
        <div class="flex items-end">
            <button type="submit"
                    class="w-full px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-xl transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 15.803a7.5 7.5 0 0 0 10.607 0Z"/></svg>
                Tampilkan Siswa
            </button>
        </div>
    </div>
</form>

{{-- ══ ATTENDANCE FORM ══ --}}
@if($classId && $students->isNotEmpty())
<form method="POST" action="{{ $storeAction }}" id="attendanceForm">
    @csrf
    <input type="hidden" name="class_id" value="{{ $classId }}">
    <input type="hidden" name="date"     value="{{ $date }}">

    {{-- ── PILIH MATA PELAJARAN GLOBAL ── --}}
    <div class="bg-slate-800/50 border border-blue-500/20 rounded-2xl p-5 backdrop-blur mb-4">
        <div class="flex items-center justify-between flex-wrap gap-2 mb-3">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                </svg>
                <p class="text-white text-sm font-semibold">Mata Pelajaran Sesi Ini</p>
                <span class="text-slate-400 text-xs">(bisa pilih lebih dari 1)</span>
            </div>
            <div class="flex items-center gap-2">
                <span id="globalCountBadge" class="hidden text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-600/20 border border-blue-500/30 text-blue-300"></span>
                <button type="button"
                        id="btnApplyAll"
                        onclick="applyGlobalToAllStudents()"
                        class="hidden items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/>
                    </svg>
                    Terapkan ke Semua Siswa
                </button>
            </div>
        </div>

        {{-- Pill buttons --}}
        <div class="flex flex-wrap gap-2" id="globalMapelContainer">
            @forelse($subjects as $subject)
            <button type="button"
                    id="gpill-{{ $subject->id }}"
                    onclick="toggleGlobal({{ $subject->id }}, '{{ addslashes($subject->name) }}')"
                    class="mapel-global-pill px-3 py-1.5 rounded-xl text-xs font-medium border border-slate-600/50 text-slate-400 hover:border-blue-400/50 hover:text-blue-300 transition-all duration-150"
                    data-id="{{ $subject->id }}"
                    data-name="{{ $subject->name }}">
                {{ $subject->name }}
            </button>
            @empty
            <p class="text-slate-500 text-xs italic">Belum ada mata pelajaran.
                @if(!$isTeacherRoute)
                    <a href="{{ route('admin.subjects.index') }}" class="text-blue-400 hover:underline">Tambah di sini →</a>
                @endif
            </p>
            @endforelse
        </div>

        {{-- Preview tag terpilih --}}
        <div id="globalTagPreview" class="hidden flex-wrap gap-1.5 pt-2.5 mt-2.5 border-t border-slate-700/40 flex"></div>
    </div>

    {{-- ── DAFTAR SISWA ── --}}
    <div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl overflow-hidden backdrop-blur">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-slate-700/50 flex items-center justify-between flex-wrap gap-3">
            <div>
                <h3 class="text-white font-semibold text-sm flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span>
                    Daftar Siswa — Kelas {{ $classrooms->firstWhere('id', $classId)?->grade_number }}
                </h3>
                <p class="text-slate-400 text-xs mt-0.5">
                    {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                    · {{ $students->count() }} siswa
                </p>
            </div>
            {{-- Bulk set all --}}
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs text-slate-400">Set semua:</span>
                @foreach($statuses as $status)
                <button type="button" onclick="setAll({{ $status->id }})"
                        class="px-3 py-1 text-xs font-medium rounded-lg border transition-colors
                               {{ $status->id == 1 ? 'border-emerald-500/50 text-emerald-300 hover:bg-emerald-500/10' : '' }}
                               {{ $status->id == 2 ? 'border-blue-500/50 text-blue-300 hover:bg-blue-500/10' : '' }}
                               {{ $status->id == 3 ? 'border-amber-500/50 text-amber-300 hover:bg-amber-500/10' : '' }}
                               {{ $status->id == 4 ? 'border-red-500/50 text-red-300 hover:bg-red-500/10' : '' }}">
                    {{ $status->name }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-slate-400 text-xs uppercase tracking-wider border-b border-slate-700/30 bg-slate-800/30">
                        <th class="px-4 py-3 text-left font-medium">#</th>
                        <th class="px-4 py-3 text-left font-medium">Nama Siswa</th>
                        <th class="px-4 py-3 text-left font-medium">NIS</th>
                        <th class="px-4 py-3 text-center font-medium">Status Sekarang</th>
                        <th class="px-4 py-3 text-center font-medium">Status Baru</th>
                        <th class="px-4 py-3 text-left font-medium">Mata Pelajaran</th>
                        <th class="px-4 py-3 text-left font-medium">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/30">
                    @foreach($students as $i => $student)
                    @php
                        $existing           = $student->attendances->first();
                        $currentStatusId    = $existing?->status_id;
                        $existingSubIds     = $existing?->subjects->pluck('id')->toArray() ?? [];
                        $existingSubNames   = $existing?->subjects->pluck('name')->join(', ') ?? '';
                        $statusColors       = [1=>'emerald', 2=>'blue', 3=>'amber', 4=>'red'];
                    @endphp
                    <tr class="hover:bg-slate-700/10 transition-colors" id="tr-{{ $student->id }}">

                        {{-- No --}}
                        <td class="px-4 py-3 text-slate-500 text-xs">{{ $i+1 }}</td>

                        {{-- Nama --}}
                        <td class="px-4 py-3 text-white font-medium">{{ $student->name }}</td>

                        {{-- NIS --}}
                        <td class="px-4 py-3 text-slate-400 text-xs">{{ $student->nis }}</td>

                        {{-- Status Sekarang --}}
                        <td class="px-4 py-3 text-center">
                            @if($existing)
                                @php $c = $statusColors[$currentStatusId] ?? 'slate'; @endphp
                                <div class="flex flex-col items-center gap-0.5">
                                    <span class="px-2 py-0.5 rounded-full text-xs text-{{ $c }}-300 bg-{{ $c }}-500/10 border border-{{ $c }}-500/30">
                                        {{ $existing->status?->name ?? '—' }}
                                        @if($existing->time_in) · {{ $existing->time_in }} @endif
                                    </span>
                                    @if($existingSubNames)
                                    <span class="text-slate-500 text-xs mt-0.5">{{ $existingSubNames }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-slate-600 text-xs">Belum absen</span>
                            @endif
                        </td>

                        {{-- Status Baru --}}
                        <td class="px-4 py-3">
                            <input type="hidden" name="attendance[{{ $i }}][student_id]" value="{{ $student->id }}">
                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                @foreach($statuses as $status)
                                @php
                                    $btnColors = [
                                        1 => ['border-emerald-500/60 text-emerald-300', 'bg-emerald-600 text-white border-emerald-500'],
                                        2 => ['border-blue-500/60 text-blue-300',        'bg-blue-600 text-white border-blue-500'],
                                        3 => ['border-amber-500/60 text-amber-300',      'bg-amber-600 text-white border-amber-500'],
                                        4 => ['border-red-500/60 text-red-300',          'bg-red-600 text-white border-red-500'],
                                    ];
                                    $colors     = $btnColors[$status->id] ?? ['border-slate-500/60 text-slate-300','bg-slate-600 text-white'];
                                    // Jika sudah ada absen → pakai status lama. Jika belum → default ke Hadir (id=1)
                                    $defaultId  = $currentStatusId ?? $statuses->first()?->id ?? 1;
                                    $isSelected = ($defaultId == $status->id);
                                @endphp
                                <label class="cursor-pointer">
                                    <input type="radio"
                                           name="attendance[{{ $i }}][status_id]"
                                           value="{{ $status->id }}"
                                           {{ $isSelected ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <span class="inline-block px-2.5 py-1 text-xs font-medium rounded-lg border transition-all peer-checked:{{ $colors[1] }} {{ $colors[0] }} hover:opacity-80">
                                        {{ $status->name }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </td>

                        {{-- Mata Pelajaran per-siswa --}}
                        <td class="px-4 py-3" style="min-width:180px">
                            {{-- Container hidden inputs — diisi JS --}}
                            <div class="student-subject-inputs" id="subInputs-{{ $i }}"></div>

                            {{-- Dropdown toggle --}}
                            <div class="relative" id="subDropWrap-{{ $i }}">
                                <button type="button"
                                        onclick="toggleSubDropdown({{ $i }})"
                                        id="subDropBtn-{{ $i }}"
                                        class="text-xs px-2.5 py-1.5 rounded-xl border border-slate-600/50 text-slate-400 hover:border-blue-400/50 hover:text-blue-300 transition-colors flex items-center gap-1.5">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                    <span id="subDropLabel-{{ $i }}">Pilih Mapel</span>
                                </button>

                                <div id="subDropMenu-{{ $i }}"
                                     class="hidden absolute left-0 top-full mt-1 p-3 rounded-xl bg-slate-700 border border-slate-600/60 shadow-xl z-20 min-w-[220px]">
                                    <p class="text-slate-400 text-xs mb-2">Pilih mapel untuk siswa ini:</p>
                                    <div class="flex flex-wrap gap-1.5" id="subDropPills-{{ $i }}">
                                        @foreach($subjects as $subject)
                                        <button type="button"
                                                id="spill-{{ $i }}-{{ $subject->id }}"
                                                onclick="toggleStudentSubject({{ $i }}, {{ $subject->id }}, '{{ addslashes($subject->name) }}')"
                                                class="px-2.5 py-1 rounded-lg text-xs font-medium border border-slate-600/50 text-slate-400 hover:border-blue-400/50 hover:text-blue-300 transition-all">
                                            {{ $subject->name }}
                                        </button>
                                        @endforeach
                                    </div>
                                    <div class="mt-2 pt-2 border-t border-slate-600/40 flex items-center justify-between">
                                        <button type="button" onclick="applyGlobalToStudent({{ $i }})"
                                                class="text-xs text-blue-400 hover:text-blue-300 transition-colors">
                                            ↩ Salin dari global
                                        </button>
                                        <button type="button" onclick="clearStudentSubjects({{ $i }})"
                                                class="text-xs text-red-400 hover:text-red-300 transition-colors">
                                            Hapus semua
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Tag preview terpilih --}}
                            <div class="flex flex-wrap gap-1 mt-1.5" id="subPreview-{{ $i }}"></div>
                        </td>

                        {{-- Catatan --}}
                        <td class="px-4 py-3">
                            <input type="text"
                                   name="attendance[{{ $i }}][notes]"
                                   value="{{ $existing?->notes ?? '' }}"
                                   placeholder="Opsional..."
                                   class="w-full bg-slate-700/40 border border-slate-600/40 text-slate-200 text-xs rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-1 focus:ring-amber-500/50">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-slate-700/50 flex items-center gap-4">
            <button type="submit"
                    class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-xl transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                Simpan Absensi
            </button>
            <span class="text-xs text-slate-500">
                Disimpan untuk tanggal {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
            </span>
        </div>
    </div>
</form>

@elseif($classId && $students->isEmpty())
<div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-12 text-center backdrop-blur">
    <div class="text-4xl mb-3">🏫</div>
    <p class="text-slate-400">Tidak ada siswa di kelas ini.</p>
</div>

@elseif(!$classId)
<div class="bg-slate-800/50 border border-amber-500/20 rounded-2xl p-8 text-center backdrop-blur">
    <div class="text-4xl mb-3">👆</div>
    <p class="text-white font-medium mb-1">Pilih kelas dan tanggal</p>
    <p class="text-slate-400 text-sm">Pilih kelas dan tanggal di form di atas, lalu klik "Tampilkan Siswa"</p>
</div>
@endif

</div>
@endsection

@push('scripts')
<script>
// ─── DATA ────────────────────────────────────────────────────────────────────
const TOTAL_STUDENTS = {{ $students->count() }};

// Global subjects yang dipilih: Set of IDs
const globalSubjectSet = new Set();

// Per-student subjects: Map of rowIndex → Set of IDs
const studentSubjectMap = {};

// Pre-load existing subjects per student (from DB)
const existingSubjects = {
    @foreach($students as $i => $student)
    @php $existing = $student->attendances->first(); $existingSubIds = $existing?->subjects->pluck('id')->toArray() ?? []; @endphp
    {{ $i }}: {{ json_encode($existingSubIds) }},
    @endforeach
};

// Initialize per-student state on page load
for (let i = 0; i < TOTAL_STUDENTS; i++) {
    studentSubjectMap[i] = new Set(existingSubjects[i] || []);
    renderStudentSubjects(i);
}

// ─── GLOBAL MAPEL ────────────────────────────────────────────────────────────
function toggleGlobal(id, name) {
    const pill = document.getElementById(`gpill-${id}`);
    if (globalSubjectSet.has(id)) {
        globalSubjectSet.delete(id);
        pill.classList.remove('bg-blue-600/30','border-blue-400/70','text-blue-200');
        pill.classList.add('border-slate-600/50','text-slate-400');
    } else {
        globalSubjectSet.add(id);
        pill.classList.remove('border-slate-600/50','text-slate-400');
        pill.classList.add('bg-blue-600/30','border-blue-400/70','text-blue-200');
    }
    renderGlobalPreview();
}

function renderGlobalPreview() {
    const preview = document.getElementById('globalTagPreview');
    const badge   = document.getElementById('globalCountBadge');
    const btnAll  = document.getElementById('btnApplyAll');
    preview.innerHTML = '';

    if (globalSubjectSet.size === 0) {
        preview.classList.add('hidden');
        badge.classList.add('hidden');
        btnAll.classList.remove('inline-flex');
        btnAll.classList.add('hidden');
        return;
    }

    preview.classList.remove('hidden');
    badge.classList.remove('hidden');
    badge.textContent = globalSubjectSet.size + ' mapel dipilih';

    // Tampilkan tombol "Terapkan ke Semua"
    btnAll.classList.remove('hidden');
    btnAll.classList.add('inline-flex');

    globalSubjectSet.forEach(id => {
        const pill = document.getElementById(`gpill-${id}`);
        const name = pill ? pill.dataset.name : id;
        const tag  = document.createElement('span');
        tag.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-600/20 text-blue-300 text-xs border border-blue-500/20';
        tag.innerHTML = `${name} <button type="button" onclick="toggleGlobal(${id},'${name}')" class="hover:text-white">✕</button>`;
        preview.appendChild(tag);
    });
}

// Terapkan mapel global ke SEMUA baris siswa sekaligus
function applyGlobalToAllStudents() {
    for (let i = 0; i < TOTAL_STUDENTS; i++) {
        // Reset dulu
        const s = studentSubjectMap[i];
        s.forEach(id => {
            const pill = document.getElementById(`spill-${i}-${id}`);
            pill?.classList.remove('bg-blue-600/30','border-blue-400/60','text-blue-200');
            pill?.classList.add('border-slate-600/50','text-slate-400');
        });
        s.clear();

        // Isi dengan global
        globalSubjectSet.forEach(id => {
            s.add(id);
            const pill = document.getElementById(`spill-${i}-${id}`);
            pill?.classList.remove('border-slate-600/50','text-slate-400');
            pill?.classList.add('bg-blue-600/30','border-blue-400/60','text-blue-200');
        });

        renderStudentSubjects(i);
    }

    // Feedback visual singkat pada tombol
    const btn = document.getElementById('btnApplyAll');
    const orig = btn.innerHTML;
    btn.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg> Diterapkan!`;
    btn.classList.remove('bg-blue-600','hover:bg-blue-700');
    btn.classList.add('bg-emerald-600');
    setTimeout(() => {
        btn.innerHTML = orig;
        btn.classList.remove('bg-emerald-600');
        btn.classList.add('bg-blue-600','hover:bg-blue-700');
    }, 1800);
}

// ─── PER-STUDENT MAPEL ───────────────────────────────────────────────────────
function toggleSubDropdown(rowIdx) {
    const menu = document.getElementById(`subDropMenu-${rowIdx}`);
    // Close all others
    document.querySelectorAll('[id^="subDropMenu-"]').forEach(m => {
        if (m.id !== `subDropMenu-${rowIdx}`) m.classList.add('hidden');
    });
    menu.classList.toggle('hidden');
}

// Close dropdowns when clicking outside
document.addEventListener('click', e => {
    if (!e.target.closest('[id^="subDropWrap-"]') && !e.target.closest('[id^="subDropMenu-"]')) {
        document.querySelectorAll('[id^="subDropMenu-"]').forEach(m => m.classList.add('hidden'));
    }
});

function toggleStudentSubject(rowIdx, subjectId, name) {
    const s = studentSubjectMap[rowIdx];
    const pill = document.getElementById(`spill-${rowIdx}-${subjectId}`);
    if (s.has(subjectId)) {
        s.delete(subjectId);
        pill?.classList.remove('bg-blue-600/30','border-blue-400/60','text-blue-200');
        pill?.classList.add('border-slate-600/50','text-slate-400');
    } else {
        s.add(subjectId);
        pill?.classList.remove('border-slate-600/50','text-slate-400');
        pill?.classList.add('bg-blue-600/30','border-blue-400/60','text-blue-200');
    }
    renderStudentSubjects(rowIdx);
}

function applyGlobalToStudent(rowIdx) {
    clearStudentSubjects(rowIdx);
    globalSubjectSet.forEach(id => {
        const pill = document.getElementById(`gpill-${id}`);
        const name = pill ? pill.dataset.name : '';
        toggleStudentSubject(rowIdx, id, name);
    });
    document.getElementById(`subDropMenu-${rowIdx}`)?.classList.add('hidden');
}

function clearStudentSubjects(rowIdx) {
    const s = studentSubjectMap[rowIdx];
    s.forEach(id => {
        const pill = document.getElementById(`spill-${rowIdx}-${id}`);
        pill?.classList.remove('bg-blue-600/30','border-blue-400/60','text-blue-200');
        pill?.classList.add('border-slate-600/50','text-slate-400');
    });
    s.clear();
    renderStudentSubjects(rowIdx);
}

function renderStudentSubjects(rowIdx) {
    const s       = studentSubjectMap[rowIdx];
    const inputs  = document.getElementById(`subInputs-${rowIdx}`);
    const preview = document.getElementById(`subPreview-${rowIdx}`);
    const label   = document.getElementById(`subDropLabel-${rowIdx}`);

    // Hidden inputs
    inputs.innerHTML = '';
    s.forEach(id => {
        const inp = document.createElement('input');
        inp.type  = 'hidden';
        inp.name  = `attendance[${rowIdx}][subject_ids][]`;
        inp.value = id;
        inputs.appendChild(inp);
    });

    // Preview tags
    preview.innerHTML = '';
    s.forEach(id => {
        const pill = document.getElementById(`gpill-${id}`);
        const name = pill ? pill.dataset.name : (document.getElementById(`spill-${rowIdx}-${id}`)?.textContent.trim() ?? id);
        const tag  = document.createElement('span');
        tag.className = 'px-1.5 py-0.5 rounded text-xs bg-blue-600/15 text-blue-300 border border-blue-500/20';
        tag.textContent = name;
        preview.appendChild(tag);
    });

    // Button label
    if (label) {
        label.textContent = s.size > 0 ? `${s.size} mapel` : 'Pilih Mapel';
    }
}

// ─── BULK STATUS ─────────────────────────────────────────────────────────────
function setAll(statusId) {
    document.querySelectorAll(`input[type="radio"][value="${statusId}"]`).forEach(r => {
        r.checked = true;
    });
}
</script>
@endpush
