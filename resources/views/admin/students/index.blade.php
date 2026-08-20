@extends('layouts.app')
@section('title', 'Data Siswa')
@section('page-title', 'Data Siswa')

@section('content')
{{-- Alert --}}
@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
     class="mb-5 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm flex items-center justify-between">
    <span>✅ {{ session('success') }}</span>
    <button @click="show=false" class="text-emerald-400 hover:text-white">✕</button>
</div>
@endif

{{-- Header + Tombol Tambah --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-white font-bold text-xl">Daftar Siswa</h2>
        <p class="text-slate-400 text-sm mt-0.5">Total: {{ $students->total() }} siswa terdaftar</p>
    </div>
    <a href="{{ route('admin.students.create') }}"
       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all shadow-lg shadow-blue-500/20">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Siswa
    </a>
</div>

{{-- Filter & Search --}}
<form method="GET" action="{{ route('admin.students.index') }}" class="flex flex-col sm:flex-row gap-3 mb-6">
    <div class="relative flex-1">
        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIS..."
               class="w-full bg-slate-800/60 border border-slate-700/50 text-white placeholder-slate-500 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
    </div>
    <select name="class" class="bg-slate-800/60 border border-slate-700/50 text-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 min-w-[160px]">
        <option value="">Semua Kelas</option>
        @foreach($classrooms as $kelas)
        <option value="{{ $kelas->id }}" {{ request('class') == $kelas->id ? 'selected' : '' }}>{{ $kelas->full_name }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-blue-600/20 border border-blue-500/30 text-blue-300 hover:bg-blue-600/30 rounded-xl px-5 py-2.5 text-sm font-medium transition-all">Filter</button>
    @if(request('search') || request('class'))
    <a href="{{ route('admin.students.index') }}" class="bg-slate-700/40 border border-slate-600/40 text-slate-400 hover:text-white rounded-xl px-4 py-2.5 text-sm transition-all">Reset</a>
    @endif
</form>

{{-- Tabel --}}
<div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl backdrop-blur overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-700/50 text-slate-400 text-xs uppercase tracking-wider">
                    <th class="px-5 py-3.5 text-left font-medium">No</th>
                    <th class="px-5 py-3.5 text-left font-medium">NIS</th>
                    <th class="px-5 py-3.5 text-left font-medium">Nama Siswa</th>
                    <th class="px-5 py-3.5 text-left font-medium">Kelas</th>
                    <th class="px-5 py-3.5 text-center font-medium">Gender</th>
                    <th class="px-5 py-3.5 text-center font-medium">RFID</th>
                    <th class="px-5 py-3.5 text-center font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/30">
                @forelse($students as $i => $siswa)
                <tr class="hover:bg-slate-700/20 transition-colors group">
                    <td class="px-5 py-3.5 text-slate-500">{{ $students->firstItem() + $i }}</td>
                    <td class="px-5 py-3.5 text-slate-300 font-mono text-xs">{{ $siswa->nis }}</td>
                    <td class="px-5 py-3.5">
                        <div class="text-white font-medium">{{ $siswa->name }}</div>
                        @if($siswa->phone)
                        <div class="text-slate-500 text-xs mt-0.5">{{ $siswa->phone }}</div>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-blue-500/10 border border-blue-500/20 text-blue-300 text-xs font-medium">
                            {{ $siswa->classroom?->full_name ?? '—' }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        <span class="text-xs {{ $siswa->gender === 'Laki-laki' ? 'text-blue-400' : 'text-pink-400' }}">
                            {{ $siswa->gender === 'Laki-laki' ? '♂' : '♀' }} {{ $siswa->gender }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        @if($siswa->rfid_code)
                        <span class="inline-flex items-center gap-1 text-xs text-emerald-400"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span>Terdaftar</span>
                        @else
                        <span class="text-xs text-slate-600">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            {{-- QR --}}
                            <button onclick="showQr('{{ $siswa->unique_code }}','{{ addslashes($siswa->name) }}','{{ $siswa->nis }}','{{ addslashes($siswa->classroom?->full_name ?? '') }}')"
                                    class="w-7 h-7 rounded-lg bg-purple-500/10 border border-purple-500/20 text-purple-400 hover:bg-purple-500/20 flex items-center justify-center transition-all" title="QR Code">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5Z"/></svg>
                            </button>
                            {{-- Edit --}}
                            <a href="{{ route('admin.students.edit', $siswa) }}"
                               class="w-7 h-7 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 hover:bg-amber-500/20 flex items-center justify-center transition-all" title="Edit">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z"/></svg>
                            </a>
                            {{-- Delete --}}
                            <form action="{{ route('admin.students.destroy', $siswa) }}" method="POST"
                                  onsubmit="return confirm('Hapus siswa {{ addslashes($siswa->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-7 h-7 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 hover:bg-red-500/20 flex items-center justify-center transition-all" title="Hapus">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-slate-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-slate-700" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                        Tidak ada data siswa ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($students->hasPages())
    <div class="px-5 py-4 border-t border-slate-700/50">
        {{ $students->links('vendor.pagination.tailwind') }}
    </div>
    @endif
</div>

{{-- QR Modal --}}
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
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
let currentQrName  = '';
let currentQrNis   = '';
let currentQrKelas = '';

function showQr(code, name, nis, kelas) {
    currentQrName  = name;
    currentQrNis   = nis   || '';
    currentQrKelas = kelas || '';
    document.getElementById('qrName').textContent = name;
    document.getElementById('qrCode').textContent = 'NIS: ' + (nis || code);
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
        const qrCanvas = document.querySelector('#qrCanvas canvas');
        const qrImg    = document.querySelector('#qrCanvas img');
        const safeName = currentQrName.replace(/[\\/:*?"<>|]/g, '').replace(/\s+/g, '_');
        const filename  = 'Kartu_Absensi_' + safeName + '.png';

        // Ukuran kartu
        const W = 400, PAD = 28;
        const headerH = 74;
        const qrSize  = 200;
        const footerH = 100;
        const H = headerH + PAD + qrSize + PAD + footerH;

        const out = document.createElement('canvas');
        out.width  = W;
        out.height = H;
        const ctx  = out.getContext('2d');

        // ── Latar belakang putih ──
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, W, H);

        // ── Garis border kartu ──
        ctx.strokeStyle = '#cbd5e1';
        ctx.lineWidth = 2;
        ctx.strokeRect(1, 1, W - 2, H - 2);

        // ── Header biru ──
        const grad = ctx.createLinearGradient(0, 0, W, headerH);
        grad.addColorStop(0, '#1d4ed8');
        grad.addColorStop(1, '#4f46e5');
        ctx.fillStyle = grad;
        ctx.fillRect(0, 0, W, headerH);

        // Teks header
        ctx.fillStyle = '#ffffff';
        ctx.textAlign = 'center';
        ctx.font = 'bold 15px Arial, sans-serif';
        ctx.fillText('SD NEGERI 30 SELAYO', W / 2, 28);
        ctx.font = '12px Arial, sans-serif';
        ctx.globalAlpha = 0.85;
        ctx.fillText('Kartu Absensi Siswa', W / 2, 50);
        ctx.globalAlpha = 1;

        // ── Garis pemisah header ──
        ctx.fillStyle = '#f1f5f9';
        ctx.fillRect(0, headerH, W, 4);

        // ── QR Code ──
        const qrY = headerH + PAD;
        if (qrCanvas) {
            // Scale ke qrSize
            ctx.drawImage(qrCanvas, (W - qrSize) / 2, qrY, qrSize, qrSize);
        } else if (qrImg) {
            ctx.drawImage(qrImg, (W - qrSize) / 2, qrY, qrSize, qrSize);
        }

        // ── Area info bawah ──
        const infoY = qrY + qrSize + PAD;
        ctx.fillStyle = '#f8fafc';
        ctx.fillRect(0, infoY - 8, W, footerH + 8);

        // Garis atas info
        ctx.fillStyle = '#e2e8f0';
        ctx.fillRect(0, infoY - 8, W, 2);

        // Nama siswa
        ctx.fillStyle = '#0f172a';
        ctx.textAlign = 'center';
        ctx.font = 'bold 18px Arial, sans-serif';
        // Potong nama jika terlalu panjang
        let displayName = currentQrName;
        while (ctx.measureText(displayName).width > W - 40 && displayName.length > 5) {
            displayName = displayName.slice(0, -1);
        }
        if (displayName !== currentQrName) displayName += '...';
        ctx.fillText(displayName, W / 2, infoY + 24);

        // NIS
        if (currentQrNis) {
            ctx.fillStyle = '#475569';
            ctx.font = '13px Arial, sans-serif';
            ctx.fillText('NIS: ' + currentQrNis, W / 2, infoY + 46);
        }

        // Kelas
        if (currentQrKelas) {
            ctx.fillStyle = '#6366f1';
            ctx.font = 'bold 12px Arial, sans-serif';
            ctx.fillText(currentQrKelas, W / 2, infoY + 68);
        }

        // Download
        const link = document.createElement('a');
        link.download = filename;
        link.href = out.toDataURL('image/png');
        link.click();
    }, 450);
}
</script>
@endpush
