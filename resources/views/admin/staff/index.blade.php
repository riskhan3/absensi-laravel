@extends('layouts.app')
@section('title', 'Data Staf & Guru')
@section('page-title', 'Data Staf & Guru')

@section('content')

{{-- Flash --}}
@if(session('success'))
<div x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4000)"
     class="mb-5 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm flex items-center justify-between">
    <span>✅ {{ session('success') }}</span>
    <button @click="show=false" class="text-emerald-400 hover:text-white">✕</button>
</div>
@endif

{{-- ── TAB NAVIGATION ── --}}
<div class="flex bg-slate-800/60 border border-slate-700/40 rounded-2xl p-1 mb-6 w-fit">
    <a href="{{ route('admin.staff.index', ['tab' => 'staff', 'search' => request('search')]) }}"
       class="px-5 py-2 rounded-xl text-sm font-semibold transition-all duration-200
              {{ $tab === 'staff' ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'text-slate-400 hover:text-slate-200' }}">
        🗂️ Staf
    </a>
    <a href="{{ route('admin.staff.index', ['tab' => 'guru', 'search' => request('search')]) }}"
       class="px-5 py-2 rounded-xl text-sm font-semibold transition-all duration-200
              {{ $tab === 'guru' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/20' : 'text-slate-400 hover:text-slate-200' }}">
        🧑‍🏫 Guru
    </a>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- TAB: STAF --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
@if($tab === 'staff')

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5">
    <div>
        <h2 class="text-white font-bold text-xl">Data Staf</h2>
        <p class="text-slate-400 text-sm mt-0.5">{{ $staffList->total() }} staf terdaftar</p>
    </div>
    <a href="{{ route('admin.staff.create') }}"
       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all shadow-lg shadow-blue-500/20">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Staf
    </a>
</div>

{{-- Search --}}
<form method="GET" action="{{ route('admin.staff.index') }}" class="flex gap-3 mb-5">
    <input type="hidden" name="tab" value="staff">
    <div class="relative flex-1 max-w-sm">
        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIP..."
               class="w-full bg-slate-800/60 border border-slate-700/50 text-white placeholder-slate-500 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
    </div>
    <button type="submit" class="bg-blue-600/20 border border-blue-500/30 text-blue-300 hover:bg-blue-600/30 rounded-xl px-5 py-2.5 text-sm font-medium transition-all">Filter</button>
    @if(request('search'))
    <a href="{{ route('admin.staff.index', ['tab' => 'staff']) }}" class="bg-slate-700/40 border border-slate-600/40 text-slate-400 hover:text-white rounded-xl px-4 py-2.5 text-sm transition-all">Reset</a>
    @endif
</form>

<div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl overflow-hidden backdrop-blur">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-700/50 text-slate-400 text-xs uppercase tracking-wider bg-slate-800/30">
                    <th class="px-5 py-3.5 text-left font-medium">No</th>
                    <th class="px-5 py-3.5 text-left font-medium">NIP</th>
                    <th class="px-5 py-3.5 text-left font-medium">Nama Staf</th>
                    <th class="px-5 py-3.5 text-left font-medium">Jabatan</th>
                    <th class="px-5 py-3.5 text-center font-medium">Gender</th>
                    <th class="px-5 py-3.5 text-left font-medium">No HP</th>
                    <th class="px-5 py-3.5 text-center font-medium">RFID</th>
                    <th class="px-5 py-3.5 text-center font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/30">
                @forelse($staffList as $i => $staf)
                <tr class="hover:bg-slate-700/20 transition-colors group">
                    <td class="px-5 py-3.5 text-slate-500">{{ $staffList->firstItem() + $i }}</td>
                    <td class="px-5 py-3.5 text-slate-400 font-mono text-xs">{{ $staf->nip ?? '—' }}</td>
                    <td class="px-5 py-3.5">
                        <div class="text-white font-medium">{{ $staf->name }}</div>
                        @if($staf->phone)
                        <div class="text-slate-500 text-xs mt-0.5">{{ $staf->phone }}</div>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-slate-300 text-xs">{{ $staf->position ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-center">
                        <span class="text-xs {{ $staf->gender === 'Laki-laki' ? 'text-blue-400' : 'text-pink-400' }}">
                            {{ $staf->gender === 'Laki-laki' ? '♂' : '♀' }} {{ $staf->gender }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-slate-400 text-xs">{{ $staf->phone ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-center">
                        @if($staf->rfid_code)
                        <span class="inline-flex items-center gap-1 text-xs text-emerald-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span>Terdaftar
                        </span>
                        @else
                        <span class="text-xs text-slate-600">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            {{-- QR --}}
                            <button onclick="showQr('{{ $staf->unique_code }}','{{ addslashes($staf->name) }}')"
                                    class="w-7 h-7 rounded-lg bg-purple-500/10 border border-purple-500/20 text-purple-400 hover:bg-purple-500/20 flex items-center justify-center transition-all" title="QR Code">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5Z"/></svg>
                            </button>
                            {{-- RFID Scan --}}
                            <button onclick="openRfid('staff','{{ $staf->id }}','{{ addslashes($staf->name) }}')"
                                    class="w-7 h-7 rounded-lg bg-teal-500/10 border border-teal-500/20 text-teal-400 hover:bg-teal-500/20 flex items-center justify-center transition-all" title="Daftarkan RFID">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1 0 12.728 0M12 3v9"/></svg>
                            </button>
                            {{-- Edit --}}
                            <a href="{{ route('admin.staff.edit', $staf) }}"
                               class="w-7 h-7 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 hover:bg-amber-500/20 flex items-center justify-center transition-all" title="Edit">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z"/></svg>
                            </a>
                            {{-- Hapus --}}
                            <form action="{{ route('admin.staff.destroy', $staf) }}" method="POST"
                                  onsubmit="return confirm('Hapus staf {{ addslashes($staf->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-7 h-7 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 hover:bg-red-500/20 flex items-center justify-center transition-all" title="Hapus">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-5 py-12 text-center text-slate-500">Belum ada data staf.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($staffList->hasPages())
    <div class="px-5 py-4 border-t border-slate-700/50">{{ $staffList->links('vendor.pagination.tailwind') }}</div>
    @endif
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- TAB: GURU --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
@else

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5">
    <div>
        <h2 class="text-white font-bold text-xl">Data Guru</h2>
        <p class="text-slate-400 text-sm mt-0.5">{{ $teacherList->total() }} guru terdaftar</p>
    </div>
    <a href="{{ route('admin.teachers.create') }}"
       class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all shadow-lg shadow-emerald-500/20">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Guru
    </a>
</div>

{{-- Search --}}
<form method="GET" action="{{ route('admin.staff.index') }}" class="flex gap-3 mb-5">
    <input type="hidden" name="tab" value="guru">
    <div class="relative flex-1 max-w-sm">
        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NUPTK..."
               class="w-full bg-slate-800/60 border border-slate-700/50 text-white placeholder-slate-500 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
    </div>
    <button type="submit" class="bg-emerald-600/20 border border-emerald-500/30 text-emerald-300 hover:bg-emerald-600/30 rounded-xl px-5 py-2.5 text-sm font-medium transition-all">Filter</button>
    @if(request('search'))
    <a href="{{ route('admin.staff.index', ['tab' => 'guru']) }}" class="bg-slate-700/40 border border-slate-600/40 text-slate-400 hover:text-white rounded-xl px-4 py-2.5 text-sm transition-all">Reset</a>
    @endif
</form>

<div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl overflow-hidden backdrop-blur">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-700/50 text-slate-400 text-xs uppercase tracking-wider bg-slate-800/30">
                    <th class="px-5 py-3.5 text-left font-medium">No</th>
                    <th class="px-5 py-3.5 text-left font-medium">NUPTK</th>
                    <th class="px-5 py-3.5 text-left font-medium">Nama Guru</th>
                    <th class="px-5 py-3.5 text-center font-medium">Gender</th>
                    <th class="px-5 py-3.5 text-left font-medium">No HP</th>
                    <th class="px-5 py-3.5 text-center font-medium">Akun Login</th>
                    <th class="px-5 py-3.5 text-center font-medium">RFID</th>
                    <th class="px-5 py-3.5 text-center font-medium">QR Code</th>
                    <th class="px-5 py-3.5 text-center font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/30">
                @forelse($teacherList as $i => $guru)
                <tr class="hover:bg-slate-700/20 transition-colors group">
                    <td class="px-5 py-3.5 text-slate-500">{{ $teacherList->firstItem() + $i }}</td>
                    <td class="px-5 py-3.5 text-slate-400 font-mono text-xs">{{ $guru->nuptk ?? '—' }}</td>
                    <td class="px-5 py-3.5">
                        <div class="text-white font-medium">{{ $guru->name }}</div>
                        @if($guru->address)
                        <div class="text-slate-500 text-xs mt-0.5 truncate max-w-[180px]">{{ $guru->address }}</div>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        <span class="text-xs {{ $guru->gender === 'Laki-laki' ? 'text-blue-400' : 'text-pink-400' }}">
                            {{ $guru->gender === 'Laki-laki' ? '♂' : '♀' }} {{ $guru->gender }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-slate-400 text-xs">{{ $guru->phone ?? '—' }}</td>
                    {{-- Kolom Akun Login --}}
                    <td class="px-5 py-3.5 text-center">
                        @if($guru->user)
                        <div class="flex flex-col items-center gap-0.5">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-xs font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span>
                                Punya Akun
                            </span>
                            <span class="text-slate-500 text-[10px]">{{ $guru->user->email }}</span>
                            <span class="text-xs text-blue-400/70">Guru</span>
                        </div>
                        @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-700/50 border border-slate-600/40 text-slate-500 text-xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-600 inline-block"></span>
                            Belum Punya
                        </span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        @if($guru->rfid_code)
                        <span class="inline-flex items-center gap-1 text-xs text-emerald-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span>Terdaftar
                        </span>
                        @else
                        <span class="text-xs text-slate-600">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        @if($guru->unique_code)
                        <span class="text-xs text-slate-400 font-mono">{{ $guru->unique_code }}</span>
                        @else
                        <span class="text-xs text-slate-600">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            {{-- QR --}}
                            <button onclick="showQr('{{ $guru->unique_code }}','{{ addslashes($guru->name) }}')"
                                    class="w-7 h-7 rounded-lg bg-purple-500/10 border border-purple-500/20 text-purple-400 hover:bg-purple-500/20 flex items-center justify-center transition-all" title="QR Code">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5Z"/></svg>
                            </button>
                            {{-- RFID Scan --}}
                            <button onclick="openRfid('teacher','{{ $guru->id }}','{{ addslashes($guru->name) }}')"
                                    class="w-7 h-7 rounded-lg bg-teal-500/10 border border-teal-500/20 text-teal-400 hover:bg-teal-500/20 flex items-center justify-center transition-all" title="Daftarkan RFID">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1 0 12.728 0M12 3v9"/></svg>
                            </button>
                            {{-- Edit --}}
                            <a href="{{ route('admin.teachers.edit', $guru) }}"
                               class="w-7 h-7 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 hover:bg-amber-500/20 flex items-center justify-center transition-all" title="Edit">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z"/></svg>
                            </a>
                            {{-- Hapus --}}
                            <form action="{{ route('admin.teachers.destroy', $guru) }}" method="POST"
                                  onsubmit="return confirm('Hapus guru {{ addslashes($guru->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-7 h-7 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 hover:bg-red-500/20 flex items-center justify-center transition-all" title="Hapus">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-5 py-12 text-center text-slate-500">Belum ada data guru.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($teacherList->hasPages())
    <div class="px-5 py-4 border-t border-slate-700/50">{{ $teacherList->links('vendor.pagination.tailwind') }}</div>
    @endif
</div>

@endif

{{-- ══ QR Modal ══ --}}
<div id="qrModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4"
     onclick="if(event.target===this) closeQr()">
    <div class="bg-slate-800 border border-slate-700/50 rounded-2xl p-6 w-full max-w-xs text-center shadow-2xl">
        <div class="w-10 h-10 rounded-xl bg-purple-500/20 border border-purple-500/30 flex items-center justify-center mx-auto mb-3">
            <svg class="w-5 h-5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5Z"/></svg>
        </div>
        <h3 class="text-white font-bold text-base mb-0.5" id="qrName">—</h3>
        <p class="text-slate-400 text-xs mb-4 font-mono" id="qrCode">—</p>
        <div id="qrImg" class="flex justify-center mb-3"></div>
        <p class="text-slate-500 text-xs mb-4">Scan QR ini di scanner absensi</p>
        <div class="flex gap-2">
            <button onclick="downloadQr()"
                    class="flex-1 bg-blue-600 hover:bg-blue-500 text-white rounded-xl py-2.5 text-sm font-semibold flex items-center justify-center gap-2 transition-all shadow-lg shadow-blue-500/20">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                Download
            </button>
            <button onclick="closeQr()"
                    class="flex-1 bg-slate-700 hover:bg-slate-600 text-white rounded-xl py-2.5 text-sm transition-all">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- ══ RFID Modal ══ --}}
<div id="rfidModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4"
     onclick="if(event.target===this) closeRfid()">
    <div class="bg-slate-800 border border-slate-700/50 rounded-2xl p-6 w-full max-w-sm">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-teal-500/20 border border-teal-500/30 flex items-center justify-center">
                <svg class="w-5 h-5 text-teal-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1 0 12.728 0M12 3v9"/></svg>
            </div>
            <div>
                <h3 class="text-white font-bold text-sm">Daftarkan Kartu RFID</h3>
                <p class="text-slate-400 text-xs" id="rfidPersonName">—</p>
            </div>
        </div>

        <form id="rfidForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs text-slate-400 mb-1.5 font-medium">Kode RFID</label>
                <div class="relative">
                    <input type="text" id="rfidInput" name="rfid_code"
                           placeholder="Tempelkan kartu atau ketik manual..."
                           class="w-full bg-slate-700/60 border border-teal-500/40 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/50 pr-10"
                           autocomplete="off">
                    <div class="absolute right-3 top-1/2 -translate-y-1/2">
                        <div id="rfidScanIndicator" class="w-2 h-2 rounded-full bg-teal-400 animate-pulse"></div>
                    </div>
                </div>
                <p class="text-slate-500 text-xs mt-1.5">Tempelkan kartu RFID ke reader, atau ketik kode secara manual</p>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 bg-teal-600 hover:bg-teal-700 text-white font-semibold py-2.5 rounded-xl text-sm transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                    Simpan RFID
                </button>
                <button type="button" onclick="closeRfid()"
                        class="px-5 py-2.5 bg-slate-700/60 text-slate-300 rounded-xl text-sm hover:bg-slate-600/60 transition-all">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
// ── QR Modal ──────────────────────────────────────────────────────────────────
let currentQrName = '';

function showQr(code, name) {
    currentQrName = name;
    document.getElementById('qrName').textContent = name;
    document.getElementById('qrCode').textContent = 'Kode: ' + code;
    document.getElementById('qrModal').classList.remove('hidden');
    document.getElementById('qrImg').innerHTML =
        '<div id="qrCanvas" class="w-48 h-48 bg-white rounded-xl p-2 flex items-center justify-center mx-auto"></div>';
    new QRCode(document.getElementById('qrCanvas'), {
        text: code, width: 176, height: 176, correctLevel: QRCode.CorrectLevel.M
    });
}

function closeQr() {
    document.getElementById('qrModal').classList.add('hidden');
}

function downloadQr() {
    setTimeout(() => {
        const canvas = document.querySelector('#qrCanvas canvas');
        const img    = document.querySelector('#qrCanvas img');
        const safeName = currentQrName.replace(/[\\/:*?"<>|]/g, '').replace(/\s+/g, '_');
        const filename  = 'QR_' + safeName + '.png';

        if (canvas) {
            // Buat canvas baru dengan nama di bawah QR
            const out = document.createElement('canvas');
            out.width  = canvas.width + 40;
            out.height = canvas.height + 60;
            const ctx = out.getContext('2d');
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, out.width, out.height);
            ctx.drawImage(canvas, 20, 10);
            ctx.fillStyle = '#1e293b';
            ctx.font = 'bold 13px sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText(currentQrName, out.width / 2, canvas.height + 32);
            const link = document.createElement('a');
            link.download = filename;
            link.href = out.toDataURL('image/png');
            link.click();
        } else if (img) {
            const link = document.createElement('a');
            link.download = filename;
            link.href = img.src;
            link.click();
        }
    }, 400);
}

// ── RFID Modal ────────────────────────────────────────────────────────────────
let rfidBuf = '', rfidTimer = null;

function openRfid(type, id, name) {
    const form = document.getElementById('rfidForm');
    // Set action URL based on type
    if (type === 'staff') {
        form.action = `/admin/staff/${id}`;
    } else {
        form.action = `/admin/teachers/${id}`;
    }
    document.getElementById('rfidPersonName').textContent = name;
    document.getElementById('rfidInput').value = '';
    document.getElementById('rfidModal').classList.remove('hidden');
    setTimeout(() => document.getElementById('rfidInput').focus(), 100);
}
function closeRfid() { document.getElementById('rfidModal').classList.add('hidden'); }

// Auto-capture RFID tap (rapid keystroke from reader)
document.getElementById('rfidInput')?.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const val = this.value.trim();
        if (val) document.getElementById('rfidForm').submit();
        return;
    }
});
</script>
@endpush
