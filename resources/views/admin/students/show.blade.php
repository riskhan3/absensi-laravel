@extends('layouts.app')

@section('title', 'Detail Siswa — ' . $student->name)
@section('page-title', 'Detail Siswa')

@section('content')
{{-- Back Button --}}
<div class="mb-6">
    <a href="{{ route('admin.students.index') }}"
       class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors group">
        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
        </svg>
        Kembali ke Daftar Siswa
    </a>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- ─── Left: Profil Siswa ─── --}}
    <div class="xl:col-span-1 space-y-4">

        {{-- Card Profil --}}
        <div class="bg-slate-800/60 border border-slate-700/50 rounded-2xl p-6 backdrop-blur">
            {{-- Avatar --}}
            <div class="flex flex-col items-center text-center mb-6">
                <div class="w-20 h-20 rounded-2xl flex items-center justify-center text-3xl font-bold mb-4
                    {{ $student->gender === 'Laki-laki' ? 'bg-blue-600/20 border border-blue-500/30 text-blue-400' : 'bg-pink-600/20 border border-pink-500/30 text-pink-400' }}">
                    {{ strtoupper(substr($student->name, 0, 1)) }}
                </div>
                <h2 class="text-xl font-bold text-white">{{ $student->name }}</h2>
                <span class="text-sm text-slate-400 mt-1">NIS: <span class="text-cyan-400 font-mono">{{ $student->nis }}</span></span>
                <span class="mt-2 inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium
                    {{ $student->gender === 'Laki-laki' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'bg-pink-500/10 text-pink-400 border border-pink-500/20' }}">
                    {{ $student->gender }}
                </span>
            </div>

            {{-- Data --}}
            <div class="space-y-3">
                <div class="flex items-center justify-between py-2.5 border-b border-slate-700/40">
                    <span class="text-xs text-slate-500 font-medium uppercase tracking-wider">Kelas</span>
                    <span class="text-sm text-white font-medium">
                        {{ $student->classroom?->full_name ?? '—' }}
                    </span>
                </div>
                <div class="flex items-center justify-between py-2.5 border-b border-slate-700/40">
                    <span class="text-xs text-slate-500 font-medium uppercase tracking-wider">No. HP Ortu</span>
                    <span class="text-sm text-white">{{ $student->phone ?? '—' }}</span>
                </div>
                <div class="flex items-center justify-between py-2.5 border-b border-slate-700/40">
                    <span class="text-xs text-slate-500 font-medium uppercase tracking-wider">Kode QR</span>
                    <span class="text-xs text-emerald-400 font-mono bg-emerald-500/10 px-2 py-1 rounded-lg">
                        {{ $student->unique_code ?? '—' }}
                    </span>
                </div>
                @if($student->rfid_code)
                <div class="flex items-center justify-between py-2.5 border-b border-slate-700/40">
                    <span class="text-xs text-slate-500 font-medium uppercase tracking-wider">RFID</span>
                    <span class="text-xs text-amber-400 font-mono bg-amber-500/10 px-2 py-1 rounded-lg">
                        {{ $student->rfid_code }}
                    </span>
                </div>
                @endif
                <div class="flex items-center justify-between py-2.5">
                    <span class="text-xs text-slate-500 font-medium uppercase tracking-wider">Terdaftar</span>
                    <span class="text-sm text-slate-300">{{ $student->created_at->translatedFormat('d F Y') }}</span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="mt-6 space-y-2">
                <a href="{{ route('admin.students.edit', $student) }}"
                   class="flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-xl bg-blue-600/20 border border-blue-500/30 text-blue-400 hover:bg-blue-600/30 transition-colors text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
                    </svg>
                    Edit Data Siswa
                </a>
                <button onclick="showQr({{ $student->id }}, '{{ $student->name }}')"
                        class="flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-xl bg-emerald-600/20 border border-emerald-500/30 text-emerald-400 hover:bg-emerald-600/30 transition-colors text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z"/>
                    </svg>
                    Lihat QR Code
                </button>
                <form action="{{ route('admin.students.destroy', $student) }}" method="POST"
                      onsubmit="return confirm('Hapus siswa {{ $student->name }}? Tindakan ini tidak bisa dibatalkan.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-xl bg-red-600/10 border border-red-500/20 text-red-400 hover:bg-red-600/20 transition-colors text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                        </svg>
                        Hapus Siswa
                    </button>
                </form>
            </div>
        </div>

        {{-- Ringkasan Absensi --}}
        @php
            $hadirCount = $recentAttendances->where('status_id', 1)->count();
            $sakitCount = $recentAttendances->where('status_id', 2)->count();
            $izinCount  = $recentAttendances->where('status_id', 3)->count();
            $alfaCount  = $recentAttendances->where('status_id', 4)->count();
            $totalRec   = $recentAttendances->count();
            $pctHadir   = $totalRec > 0 ? round(($hadirCount / $totalRec) * 100) : 0;
        @endphp
        <div class="bg-slate-800/60 border border-slate-700/50 rounded-2xl p-5 backdrop-blur">
            <h3 class="text-sm font-semibold text-white mb-4">Ringkasan 30 Hari Terakhir</h3>
            <div class="space-y-3">
                @foreach([
                    ['label'=>'Hadir',  'count'=>$hadirCount, 'color'=>'emerald', 'pct'=>$totalRec>0 ? ($hadirCount/$totalRec)*100 : 0],
                    ['label'=>'Sakit',  'count'=>$sakitCount, 'color'=>'blue',    'pct'=>$totalRec>0 ? ($sakitCount/$totalRec)*100 : 0],
                    ['label'=>'Izin',   'count'=>$izinCount,  'color'=>'amber',   'pct'=>$totalRec>0 ? ($izinCount/$totalRec)*100 : 0],
                    ['label'=>'Alfa',   'count'=>$alfaCount,  'color'=>'red',     'pct'=>$totalRec>0 ? ($alfaCount/$totalRec)*100 : 0],
                ] as $s)
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-{{ $s['color'] }}-400">{{ $s['label'] }}</span>
                        <span class="text-slate-400">{{ $s['count'] }} hari</span>
                    </div>
                    <div class="h-1.5 bg-slate-700/50 rounded-full overflow-hidden">
                        <div class="h-full bg-{{ $s['color'] }}-500 rounded-full transition-all duration-500"
                             style="width: {{ $s['pct'] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-4 pt-4 border-t border-slate-700/40 flex items-center justify-between">
                <span class="text-xs text-slate-500">Tingkat Kehadiran</span>
                <span class="text-lg font-bold {{ $pctHadir >= 80 ? 'text-emerald-400' : ($pctHadir >= 60 ? 'text-amber-400' : 'text-red-400') }}">
                    {{ $pctHadir }}%
                </span>
            </div>
        </div>
    </div>

    {{-- ─── Right: Riwayat Absensi ─── --}}
    <div class="xl:col-span-2">
        <div class="bg-slate-800/60 border border-slate-700/50 rounded-2xl overflow-hidden backdrop-blur">
            <div class="px-6 py-4 border-b border-slate-700/40 flex items-center justify-between">
                <div>
                    <h3 class="text-white font-semibold">Riwayat Absensi</h3>
                    <p class="text-xs text-slate-400 mt-0.5">30 catatan terbaru</p>
                </div>
                <span class="text-xs text-slate-500 bg-slate-700/50 px-2.5 py-1 rounded-full">
                    {{ $recentAttendances->count() }} catatan
                </span>
            </div>

            @if($recentAttendances->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-14 h-14 rounded-2xl bg-slate-700/50 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                    </svg>
                </div>
                <p class="text-slate-400 text-sm font-medium">Belum ada catatan absensi</p>
                <p class="text-slate-600 text-xs mt-1">Absensi siswa ini belum pernah dicatat</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-700/40">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Masuk</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden lg:table-cell">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/30">
                        @foreach($recentAttendances as $att)
                        @php
                            $statusColors = [
                                1 => ['bg'=>'bg-emerald-500/10', 'text'=>'text-emerald-400', 'border'=>'border-emerald-500/20'],
                                2 => ['bg'=>'bg-blue-500/10',    'text'=>'text-blue-400',    'border'=>'border-blue-500/20'],
                                3 => ['bg'=>'bg-amber-500/10',   'text'=>'text-amber-400',   'border'=>'border-amber-500/20'],
                                4 => ['bg'=>'bg-red-500/10',     'text'=>'text-red-400',     'border'=>'border-red-500/20'],
                            ];
                            $sc = $statusColors[$att->status_id] ?? ['bg'=>'bg-slate-500/10','text'=>'text-slate-400','border'=>'border-slate-500/20'];
                        @endphp
                        <tr class="hover:bg-slate-700/20 transition-colors">
                            <td class="px-5 py-3.5">
                                <p class="text-white font-medium">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('d F Y') }}</p>
                                <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('l') }}</p>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border
                                    {{ $sc['bg'] }} {{ $sc['text'] }} {{ $sc['border'] }}">
                                    {{ $att->status?->name ?? '—' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 hidden md:table-cell text-slate-300 font-mono text-xs">
                                {{ $att->time_in ?? '—' }}
                            </td>
                            <td class="px-5 py-3.5 hidden lg:table-cell text-slate-400 text-xs max-w-xs truncate">
                                {{ $att->notes ?: '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ─── QR Modal ─── --}}
<div id="qrModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm" onclick="closeQr(event)">
    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-8 max-w-xs w-full text-center shadow-2xl" onclick="event.stopPropagation()">
        <h4 id="qrName" class="text-white font-semibold mb-5 text-lg"></h4>
        <div id="qrContainer" class="w-52 h-52 mx-auto bg-white rounded-xl flex items-center justify-center">
            <div class="text-slate-400 text-sm">Memuat...</div>
        </div>
        <p id="qrCode" class="mt-4 font-mono text-xs text-slate-400"></p>
        <button onclick="document.getElementById('qrModal').classList.add('hidden'); document.getElementById('qrModal').classList.remove('flex');"
                class="mt-6 w-full px-4 py-2.5 rounded-xl bg-slate-700 text-slate-300 hover:bg-slate-600 transition-colors text-sm">
            Tutup
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
function showQr(id, name) {
    const modal = document.getElementById('qrModal');
    document.getElementById('qrName').textContent = name;
    document.getElementById('qrContainer').innerHTML = '<div class="text-slate-400 text-sm">Memuat...</div>';
    document.getElementById('qrCode').textContent = '';
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    fetch(`/admin/students/${id}/qr`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('qrContainer').innerHTML = '';
            document.getElementById('qrCode').textContent = data.code;
            new QRCode(document.getElementById('qrContainer'), {
                text: data.code,
                width: 192,
                height: 192,
                colorDark: '#1e293b',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
        });
}
function closeQr(event) {
    document.getElementById('qrModal').classList.add('hidden');
    document.getElementById('qrModal').classList.remove('flex');
}
</script>
@endpush
