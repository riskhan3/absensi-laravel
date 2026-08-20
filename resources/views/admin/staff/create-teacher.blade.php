@extends('layouts.app')
@section('title', isset($teacher) ? 'Edit Guru' : 'Tambah Guru')
@section('page-title', isset($teacher) ? 'Edit Data Guru' : 'Tambah Guru Baru')

@section('content')
<div class="max-w-xl">
<div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-6 backdrop-blur">

    <form action="{{ isset($teacher) ? route('admin.teachers.update', $teacher) : route('admin.teachers.store') }}"
          method="POST" class="space-y-4">
        @csrf
        @if(isset($teacher)) @method('PUT') @endif

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">NUPTK <span class="text-slate-500 font-normal">(Opsional)</span></label>
                <input type="text" name="nuptk" value="{{ old('nuptk', $teacher->nuptk ?? '') }}" maxlength="20"
                       class="w-full bg-slate-700/50 border border-slate-600/60 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                       placeholder="NUPTK guru">
                @error('nuptk')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Jenis Kelamin <span class="text-red-400">*</span></label>
                <select name="gender" required class="w-full bg-slate-700/50 border border-slate-600/60 text-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                    <option value="Laki-laki" {{ old('gender', $teacher->gender ?? '') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="Perempuan" {{ old('gender', $teacher->gender ?? '') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
            <input type="text" name="name" value="{{ old('name', $teacher->name ?? '') }}" required
                   class="w-full bg-slate-700/50 border border-slate-600/60 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
            @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1.5">No HP</label>
            <input type="text" name="phone" value="{{ old('phone', $teacher->phone ?? '') }}" maxlength="32"
                   class="w-full bg-slate-700/50 border border-slate-600/60 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                   placeholder="08xxxxxxxxxx">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1.5">Alamat</label>
            <input type="text" name="address" value="{{ old('address', $teacher->address ?? '') }}" maxlength="500"
                   class="w-full bg-slate-700/50 border border-slate-600/60 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                   placeholder="Alamat guru">
        </div>

        {{-- ── RFID / Barcode ── --}}
        <div class="rounded-xl border border-teal-500/20 bg-teal-500/5 p-4 space-y-3">
            <div class="flex items-center gap-2 mb-1">
                <svg class="w-4 h-4 text-teal-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1 0 12.728 0M12 3v9"/>
                </svg>
                <span class="text-teal-300 text-sm font-semibold">Kartu Absensi</span>
                <span class="text-slate-500 text-xs">(RFID / Barcode — opsional)</span>
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1.5">Kode RFID</label>
                <div class="flex gap-2">
                    <input type="text" name="rfid_code" id="rfidField"
                           value="{{ old('rfid_code', $teacher->rfid_code ?? '') }}"
                           placeholder="Tempelkan kartu atau ketik kode..."
                           class="flex-1 bg-slate-700/50 border border-slate-600/60 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/50"
                           autocomplete="off">
                    <button type="button" onclick="clearRfid()"
                            class="px-3 py-2 rounded-xl text-xs text-red-400 border border-red-500/30 hover:bg-red-500/10 transition-colors">
                        Hapus
                    </button>
                </div>
                @error('rfid_code')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                <p class="text-slate-500 text-xs mt-1.5">Tempelkan kartu RFID ke reader saat field ini aktif — kode akan terisi otomatis</p>
            </div>
        </div>

        {{-- ── PILIH MATA PELAJARAN ── --}}
        <div class="rounded-xl border border-emerald-500/20 bg-emerald-500/5 p-4 space-y-3">
            <div class="flex items-center gap-2 mb-1">
                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.966 8.966 0 0 0-6 2.292m0-14.25v14.25"/>
                </svg>
                <span class="text-emerald-300 text-sm font-semibold">Mata Pelajaran yang Diajarkan</span>
                <span class="text-slate-500 text-xs">(opsional — bisa lebih dari 1)</span>
            </div>
            @if(isset($subjects) && $subjects->isNotEmpty())
                @php
                    $selectedIds = isset($teacher) ? $teacher->subjects->pluck('id')->toArray() : old('subject_ids', []);
                @endphp
                <div class="grid grid-cols-2 gap-2">
                    @foreach($subjects as $subject)
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <input type="checkbox"
                               name="subject_ids[]"
                               value="{{ $subject->id }}"
                               {{ in_array($subject->id, (array)$selectedIds) ? 'checked' : '' }}
                               class="w-4 h-4 rounded text-emerald-500 bg-slate-700 border-slate-500 focus:ring-emerald-500/40">
                        <span class="text-sm text-slate-300 group-hover:text-white transition-colors">
                            <span class="text-xs font-bold text-emerald-400 mr-1">{{ $subject->code }}</span>
                            {{ $subject->name }}
                        </span>
                    </label>
                    @endforeach
                </div>
                @error('subject_ids')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            @else
                <p class="text-slate-500 text-xs">Tidak ada mata pelajaran aktif.</p>
            @endif
        </div>

        {{-- ── AKUN LOGIN GURU ── --}}
        <div class="rounded-xl border border-blue-500/20 bg-blue-500/5 p-4 space-y-3">
            <div class="flex items-center gap-2 mb-1">
                <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 0 1 21.75 8.25Z"/>
                </svg>
                <span class="text-blue-300 text-sm font-semibold">Akun Login Guru</span>
                @if(isset($teacher) && $teacher->user)
                    <span class="ml-auto inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-xs font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span>
                        Sudah Punya Akun
                    </span>
                @else
                    <span class="text-slate-500 text-xs">(opsional — isi untuk membuat akun login)</span>
                @endif
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-xs text-slate-400 mb-1.5">Email Login</label>
                <input type="email" name="email"
                       value="{{ old('email', $teacher->user->email ?? '') }}"
                       placeholder="email@sekolah.com"
                       class="w-full bg-slate-700/50 border border-slate-600/60 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                @error('email')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Role --}}
            <div>
                <label class="block text-xs text-slate-400 mb-1.5">Peran / Role</label>
                <select name="role"
                        class="w-full bg-slate-700/50 border border-slate-600/60 text-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                    <option value="guru_mapel" {{ old('role', $teacher->user->role ?? 'guru_mapel') === 'guru_mapel' ? 'selected' : '' }}>Guru Mata Pelajaran</option>
                    <option value="wali_kelas" {{ old('role', $teacher->user->role ?? '') === 'wali_kelas' ? 'selected' : '' }}>Wali Kelas</option>
                </select>
                @error('role')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Password --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-slate-400 mb-1.5">
                        Password {{ isset($teacher) && $teacher->user ? '<span class="text-slate-600">(kosong = tidak diganti)</span>' : '<span class="text-red-400">*</span>' }}
                    </label>
                    <input type="password" name="password"
                           placeholder="{{ isset($teacher) && $teacher->user ? 'Kosongkan jika tidak diganti' : 'Min. 6 karakter' }}"
                           class="w-full bg-slate-700/50 border border-slate-600/60 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                    @error('password')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1.5">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation"
                           placeholder="Ulangi password"
                           class="w-full bg-slate-700/50 border border-slate-600/60 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                </div>
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="flex-1 {{ isset($teacher) ? 'bg-amber-600 hover:bg-amber-500 shadow-amber-500/20' : 'bg-emerald-600 hover:bg-emerald-500 shadow-emerald-500/20' }} text-white font-semibold py-2.5 rounded-xl text-sm transition-all shadow-lg">
                💾 {{ isset($teacher) ? 'Perbarui' : 'Simpan' }}
            </button>
            <a href="{{ route('admin.staff.index', ['tab' => 'guru']) }}"
               class="px-6 py-2.5 bg-slate-700/60 text-slate-300 rounded-xl text-sm hover:bg-slate-600/60 transition-all">Batal</a>
        </div>
    </form>
</div>
</div>



@endsection

@push('scripts')
<script>
function clearRfid() {
    document.getElementById('rfidField').value = '';
    document.getElementById('rfidField').focus();
}
</script>
@endpush
