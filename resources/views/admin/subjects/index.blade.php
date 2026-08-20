@extends('layouts.app')
@section('title', 'Mata Pelajaran')
@section('page-title', 'Mata Pelajaran')

@section('content')
@if(session('success'))
<div x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4000)"
     class="mb-5 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm flex items-center justify-between">
    <span>✅ {{ session('success') }}</span><button @click="show=false">✕</button>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

{{-- Daftar Mapel --}}
<div class="lg:col-span-2">
    <div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl overflow-hidden backdrop-blur">
        <div class="px-5 py-4 border-b border-slate-700/50">
            <h3 class="text-white font-semibold">Daftar Mata Pelajaran</h3>
        </div>

        {{-- Kelas 1-2 --}}
        @foreach(['all' => '📚 Semua Kelas', '1-2' => '🟢 Kelas 1–2', '3-6' => '🔵 Kelas 3–6'] as $group => $label)
        @php $grouped = $subjects->where('class_group', $group) @endphp
        @if($grouped->count())
        <div class="px-5 py-2 bg-slate-700/20 border-b border-slate-700/30">
            <p class="text-slate-400 text-xs font-semibold uppercase">{{ $label }}</p>
        </div>
        @foreach($grouped as $subj)
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-700/20 last:border-0 hover:bg-slate-700/20 transition-colors group">
            <div class="flex items-center gap-3">
                <span class="inline-flex px-2.5 py-0.5 rounded-md text-xs font-bold
                    {{ $group === 'all' ? 'bg-slate-700 text-slate-300' : ($group === '1-2' ? 'bg-emerald-500/15 text-emerald-300' : 'bg-blue-500/15 text-blue-300') }}">
                    {{ $subj->code }}
                </span>
                <span class="text-white text-sm">{{ $subj->name }}</span>
                @if(!$subj->is_active)
                <span class="text-xs px-2 py-0.5 rounded bg-red-500/10 border border-red-500/20 text-red-400">Nonaktif</span>
                @endif
            </div>
            <form action="{{ route('admin.subjects.destroy', $subj) }}" method="POST"
                  class="opacity-0 group-hover:opacity-100 transition-opacity"
                  onsubmit="return confirm('Hapus mata pelajaran ini?')">
                @csrf @method('DELETE')
                <button class="w-7 h-7 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 hover:bg-red-500/20 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                </button>
            </form>
        </div>
        @endforeach
        @endif
        @endforeach
    </div>
</div>

{{-- Form Tambah --}}
<div>
    <div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-5 backdrop-blur sticky top-24">
        <h3 class="text-white font-semibold mb-4">Tambah Mata Pelajaran</h3>
        <form action="{{ route('admin.subjects.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1">Nama Mapel <span class="text-red-400">*</span></label>
                <input type="text" name="name" required maxlength="100"
                       class="w-full bg-slate-700/50 border border-slate-600/60 text-white rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                       placeholder="Bahasa Indonesia">
                @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1">Kode <span class="text-red-400">*</span></label>
                <input type="text" name="code" required maxlength="20"
                       class="w-full bg-slate-700/50 border border-slate-600/60 text-white rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 uppercase"
                       placeholder="BIND">
                @error('code')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1">Berlaku untuk Kelas <span class="text-red-400">*</span></label>
                <select name="class_group" required class="w-full bg-slate-700/50 border border-slate-600/60 text-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                    <option value="all">Semua Kelas (1–6)</option>
                    <option value="1-2">Kelas 1–2 Saja</option>
                    <option value="3-6">Kelas 3–6 Saja</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2.5 rounded-xl text-sm transition-all shadow-lg shadow-blue-500/20">
                + Tambahkan
            </button>
        </form>
    </div>
</div>

</div>
@endsection
