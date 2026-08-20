@extends('layouts.app')
@section('title', 'Tambah Siswa')
@section('page-title', 'Tambah Siswa Baru')

@section('content')
<div class="max-w-2xl">
    <div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-6 backdrop-blur">
        <form action="{{ route('admin.students.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- NIS --}}
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">NIS <span class="text-red-400">*</span></label>
                <input type="text" name="nis" value="{{ old('nis') }}" required maxlength="16"
                       class="w-full bg-slate-700/50 border {{ $errors->has('nis') ? 'border-red-500/60' : 'border-slate-600/60' }} text-white placeholder-slate-500 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all"
                       placeholder="Contoh: 2025001">
                @error('nis')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Nama --}}
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full bg-slate-700/50 border {{ $errors->has('name') ? 'border-red-500/60' : 'border-slate-600/60' }} text-white placeholder-slate-500 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all"
                       placeholder="Nama lengkap siswa">
                @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Kelas & Gender --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Kelas <span class="text-red-400">*</span></label>
                    <select name="classroom_id" required
                            class="w-full bg-slate-700/50 border {{ $errors->has('classroom_id') ? 'border-red-500/60' : 'border-slate-600/60' }} text-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classrooms as $kelas)
                        <option value="{{ $kelas->id }}" {{ old('classroom_id') == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->full_name }}
                        </option>
                        @endforeach
                    </select>
                    @error('classroom_id')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Jenis Kelamin <span class="text-red-400">*</span></label>
                    <select name="gender" required
                            class="w-full bg-slate-700/50 border {{ $errors->has('gender') ? 'border-red-500/60' : 'border-slate-600/60' }} text-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all">
                        <option value="">-- Pilih --</option>
                        <option value="Laki-laki" {{ old('gender') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('gender') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- No HP --}}
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">No HP Orang Tua/Wali</label>
                <input type="text" name="phone" value="{{ old('phone') }}" maxlength="32"
                       class="w-full bg-slate-700/50 border border-slate-600/60 text-white placeholder-slate-500 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all"
                       placeholder="08xxxxxxxxxx (untuk notifikasi WA)">
                <p class="text-slate-500 text-xs mt-1">Akan digunakan untuk notifikasi WhatsApp.</p>
            </div>

            {{-- RFID --}}
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Kode RFID <span class="text-slate-500 font-normal">(Opsional)</span></label>
                <input type="text" name="rfid_code" value="{{ old('rfid_code') }}" maxlength="100"
                       class="w-full bg-slate-700/50 border {{ $errors->has('rfid_code') ? 'border-red-500/60' : 'border-slate-600/60' }} text-white placeholder-slate-500 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all"
                       placeholder="Tempelkan kartu RFID atau isi manual">
                @error('rfid_code')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Buttons --}}
            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2.5 rounded-xl transition-all text-sm shadow-lg shadow-blue-500/20">
                    💾 Simpan Siswa
                </button>
                <a href="{{ route('admin.students.index') }}"
                   class="px-6 py-2.5 bg-slate-700/60 hover:bg-slate-600/60 text-slate-300 rounded-xl text-sm font-medium transition-all">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
