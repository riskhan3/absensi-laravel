@extends('layouts.app')
@section('title', isset($staff) ? 'Edit Staf' : 'Tambah Staf')
@section('page-title', isset($staff) ? 'Edit Data Staf' : 'Tambah Staf Baru')

@section('content')
<div class="max-w-xl">
<div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-6 backdrop-blur">
    <form action="{{ isset($staff) ? route('admin.staff.update', $staff) : route('admin.staff.store') }}" method="POST" class="space-y-4">
        @csrf
        @if(isset($staff)) @method('PUT') @endif

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">NIP <span class="text-slate-500 font-normal">(Opsional)</span></label>
                <input type="text" name="nip" value="{{ old('nip', $staff->nip ?? '') }}" maxlength="30"
                       class="w-full bg-slate-700/50 border border-slate-600/60 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                       placeholder="NIP staf">
                @error('nip')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Jenis Kelamin <span class="text-red-400">*</span></label>
                <select name="gender" required class="w-full bg-slate-700/50 border border-slate-600/60 text-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                    <option value="Laki-laki" {{ old('gender', $staff->gender ?? '') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="Perempuan" {{ old('gender', $staff->gender ?? '') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
            <input type="text" name="name" value="{{ old('name', $staff->name ?? '') }}" required
                   class="w-full bg-slate-700/50 border border-slate-600/60 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
            @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1.5">Jabatan</label>
            <input type="text" name="position" value="{{ old('position', $staff->position ?? '') }}" maxlength="100"
                   class="w-full bg-slate-700/50 border border-slate-600/60 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                   placeholder="Staf TU, Penjaga Sekolah, dll">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1.5">No HP</label>
            <input type="text" name="phone" value="{{ old('phone', $staff->phone ?? '') }}" maxlength="32"
                   class="w-full bg-slate-700/50 border border-slate-600/60 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                   placeholder="08xxxxxxxxxx">
        </div>

        {{-- RFID / Barcode --}}
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
                    <input type="text" name="rfid_code" id="rfidFieldStaf"
                           value="{{ old('rfid_code', $staff->rfid_code ?? '') }}"
                           placeholder="Tempelkan kartu atau ketik kode..."
                           class="flex-1 bg-slate-700/50 border border-slate-600/60 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/50"
                           autocomplete="off">
                    <button type="button" onclick="document.getElementById('rfidFieldStaf').value='';document.getElementById('rfidFieldStaf').focus();"
                            class="px-3 py-2 rounded-xl text-xs text-red-400 border border-red-500/30 hover:bg-red-500/10 transition-colors">
                        Hapus
                    </button>
                </div>
                @error('rfid_code')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                <p class="text-slate-500 text-xs mt-1.5">Tempelkan kartu RFID ke reader saat field ini aktif — kode akan terisi otomatis</p>
            </div>
        </div>

        @if(isset($staff) && $staff->unique_code)
        <div class="rounded-xl border border-purple-500/20 bg-purple-500/5 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-300 text-xs font-semibold mb-0.5">QR Code Absensi</p>
                    <p class="text-slate-400 text-xs font-mono">{{ $staff->unique_code }}</p>
                </div>
                <button type="button" onclick="alert('Buka halaman daftar staf untuk lihat QR.')"
                        class="px-3 py-1.5 rounded-lg text-xs font-medium bg-purple-600/20 border border-purple-500/30 text-purple-300 hover:bg-purple-600/30 transition-colors">
                    Lihat QR
                </button>
            </div>
        </div>
        @endif

        <div class="flex gap-3 pt-2">
            <button type="submit" class="flex-1 {{ isset($staff) ? 'bg-amber-600 hover:bg-amber-500 shadow-amber-500/20' : 'bg-blue-600 hover:bg-blue-500 shadow-blue-500/20' }} text-white font-semibold py-2.5 rounded-xl text-sm transition-all shadow-lg">
                💾 {{ isset($staff) ? 'Perbarui' : 'Simpan' }}
            </button>
            <a href="{{ route('admin.staff.index') }}" class="px-6 py-2.5 bg-slate-700/60 text-slate-300 rounded-xl text-sm hover:bg-slate-600/60 transition-all">Batal</a>
        </div>
    </form>
</div>
</div>
@endsection
