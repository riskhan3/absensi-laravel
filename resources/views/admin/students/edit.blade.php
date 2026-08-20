@extends('layouts.app')
@section('title', 'Edit Siswa')
@section('page-title', 'Edit Data Siswa')

@section('content')
<div class="max-w-2xl">
    <div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-6 backdrop-blur">

        {{-- Info Siswa --}}
        <div class="flex items-center gap-4 mb-6 p-4 bg-slate-700/30 rounded-xl border border-slate-600/30">
            <div class="w-12 h-12 rounded-xl bg-blue-600/20 border border-blue-500/30 flex items-center justify-center text-blue-300 text-lg font-bold flex-shrink-0">
                {{ strtoupper(substr($student->name, 0, 1)) }}
            </div>
            <div>
                <p class="text-white font-semibold">{{ $student->name }}</p>
                <p class="text-slate-400 text-xs">NIS: {{ $student->nis }} · Kode: {{ $student->unique_code }}</p>
            </div>
        </div>

        <form action="{{ route('admin.students.update', $student) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">NIS <span class="text-red-400">*</span></label>
                    <input type="text" name="nis" value="{{ old('nis', $student->nis) }}" required maxlength="16"
                           class="w-full bg-slate-700/50 border {{ $errors->has('nis') ? 'border-red-500/60' : 'border-slate-600/60' }} text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                    @error('nis')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Jenis Kelamin <span class="text-red-400">*</span></label>
                    <select name="gender" required class="w-full bg-slate-700/50 border border-slate-600/60 text-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                        <option value="Laki-laki"  {{ old('gender', $student->gender) === 'Laki-laki'  ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan"  {{ old('gender', $student->gender) === 'Perempuan'  ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
                <input type="text" name="name" value="{{ old('name', $student->name) }}" required
                       class="w-full bg-slate-700/50 border {{ $errors->has('name') ? 'border-red-500/60' : 'border-slate-600/60' }} text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Kelas <span class="text-red-400">*</span></label>
                <select name="classroom_id" required class="w-full bg-slate-700/50 border {{ $errors->has('classroom_id') ? 'border-red-500/60' : 'border-slate-600/60' }} text-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                    @foreach($classrooms as $kelas)
                    <option value="{{ $kelas->id }}" {{ old('classroom_id', $student->classroom_id) == $kelas->id ? 'selected' : '' }}>
                        {{ $kelas->full_name }}
                    </option>
                    @endforeach
                </select>
                @error('classroom_id')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">No HP Orang Tua/Wali</label>
                <input type="text" name="phone" value="{{ old('phone', $student->phone) }}" maxlength="32"
                       class="w-full bg-slate-700/50 border border-slate-600/60 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                       placeholder="08xxxxxxxxxx">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Kode RFID <span class="text-slate-500">(Opsional)</span></label>
                <input type="text" name="rfid_code" value="{{ old('rfid_code', $student->rfid_code) }}" maxlength="100"
                       class="w-full bg-slate-700/50 border {{ $errors->has('rfid_code') ? 'border-red-500/60' : 'border-slate-600/60' }} text-white font-mono rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                @error('rfid_code')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-amber-600 hover:bg-amber-500 text-white font-semibold py-2.5 rounded-xl transition-all text-sm shadow-lg shadow-amber-500/20">
                    💾 Perbarui Data
                </button>
                <a href="{{ route('admin.students.index') }}" class="px-6 py-2.5 bg-slate-700/60 hover:bg-slate-600/60 text-slate-300 rounded-xl text-sm font-medium transition-all">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
