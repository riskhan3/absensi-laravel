@extends('layouts.app')

@section('title', 'Dashboard Guru')
@section('page-title', 'Dashboard Guru')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">

    {{-- Animated Icon --}}
    <div class="relative mb-8">
        <div class="w-28 h-28 rounded-3xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center mx-auto animate-pulse">
            <svg class="w-14 h-14 text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 10.741-2.342M12 13.489V21m0 0a48.71 48.71 0 0 1-8.232-4.41 60.46 60.46 0 0 0-.491-6.347m8.723 10.757V21" />
            </svg>
        </div>
        <div class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-amber-500 flex items-center justify-center shadow-lg shadow-amber-500/30">
            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
            </svg>
        </div>
    </div>

    {{-- Heading --}}
    <h1 class="text-2xl font-bold text-white mb-3">Belum Ada Kelas yang Ditugaskan</h1>
    <p class="text-slate-400 max-w-md mb-2">
        Akun Anda belum ditetapkan sebagai <span class="text-amber-400 font-medium">wali kelas</span> untuk kelas tertentu.
        Hubungi Admin untuk mengatur penugasan kelas Anda.
    </p>
    <p class="text-slate-500 text-sm mb-8">
        Anda tetap bisa menggunakan fitur scanner dan input absensi manual di bawah ini.
    </p>

    {{-- Action Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 w-full max-w-2xl mb-8">
        <a href="{{ route('teacher.classroom-scan.index') }}"
           class="group flex flex-col items-center gap-3 p-5 rounded-2xl bg-blue-600/10 border border-blue-500/20 hover:bg-blue-600/20 hover:border-blue-500/40 transition-all duration-200">
            <div class="w-12 h-12 rounded-xl bg-blue-600/20 border border-blue-500/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
                </svg>
            </div>
            <div>
                <p class="text-white font-semibold text-sm">Scan QR/RFID</p>
                <p class="text-slate-400 text-xs mt-0.5">Absensi kelas real-time</p>
            </div>
        </a>

        <a href="{{ route('teacher.sessions.create') }}"
           class="group flex flex-col items-center gap-3 p-5 rounded-2xl bg-emerald-600/10 border border-emerald-500/20 hover:bg-emerald-600/20 hover:border-emerald-500/40 transition-all duration-200">
            <div class="w-12 h-12 rounded-xl bg-emerald-600/20 border border-emerald-500/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                </svg>
            </div>
            <div>
                <p class="text-white font-semibold text-sm">Input Manual</p>
                <p class="text-slate-400 text-xs mt-0.5">Isi daftar hadir manual</p>
            </div>
        </a>

        <a href="{{ route('teacher.sessions.index') }}"
           class="group flex flex-col items-center gap-3 p-5 rounded-2xl bg-violet-600/10 border border-violet-500/20 hover:bg-violet-600/20 hover:border-violet-500/40 transition-all duration-200">
            <div class="w-12 h-12 rounded-xl bg-violet-600/20 border border-violet-500/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-violet-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z"/>
                </svg>
            </div>
            <div>
                <p class="text-white font-semibold text-sm">Riwayat Sesi</p>
                <p class="text-slate-400 text-xs mt-0.5">Lihat catatan absensi</p>
            </div>
        </a>
    </div>

    {{-- Info contact admin --}}
    <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-800/60 border border-slate-700/50 text-left max-w-md">
        <svg class="w-5 h-5 text-cyan-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>
        </svg>
        <div>
            <p class="text-sm font-medium text-white">Butuh bantuan admin?</p>
            <p class="text-xs text-slate-400 mt-1">
                Admin perlu menetapkan Anda sebagai wali kelas di menu
                <span class="text-cyan-400 font-medium">Data Staf &amp; Guru → Edit Guru</span>.
                Setelah ditetapkan, dashboard ini akan menampilkan statistik kelas Anda.
            </p>
        </div>
    </div>
</div>
@endsection
