<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Scanner — SD Negeri 30 Selayo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
        .float { animation: float 3s ease-in-out infinite; }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-6px) scale(1.02); }
    </style>
</head>
<body class="h-full bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 flex items-center justify-center px-4">

    <!-- BG decorations -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-purple-500/5 rounded-full blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-lg">

        <!-- User Info Bar (Guru yang Login) -->
        <div class="flex items-center justify-between mb-6 px-1">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-600/20 border border-blue-500/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white text-sm font-semibold leading-tight">{{ $user->name }}</p>
                    <p class="text-slate-400 text-xs leading-tight">
                        @if($teacher)
                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                            @if($teacher->nuptk) · NUPTK: {{ $teacher->nuptk }} @endif
                        @else
                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                        @endif
                    </p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs text-red-400 border border-red-500/30 hover:bg-red-500/10 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>

        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-blue-600/20 border border-blue-500/30 mb-4 float">
                <svg class="w-9 h-9 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white mb-1">Scanner Absensi</h1>
            <p class="text-slate-400 text-sm">SD Negeri 30 Selayo</p>
            <div class="text-slate-500 text-xs mt-1" id="clockDisplay"></div>
        </div>

        <!-- Scanner Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

            <!-- Scanner 1: Siswa -->
            <a href="{{ route('scan.siswa') }}" id="btn-scanner-siswa"
               class="card-hover group block bg-slate-800/50 backdrop-blur border border-blue-500/30 rounded-3xl p-7 text-center hover:bg-blue-600/10 hover:border-blue-400/60 hover:shadow-2xl hover:shadow-blue-500/20">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-blue-600/20 border border-blue-500/30 mb-5 group-hover:bg-blue-600/30 transition-colors">
                    <svg class="w-9 h-9 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                    </svg>
                </div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/30 mb-4">
                    <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                    <span class="text-blue-300 text-xs font-medium">Scanner 1</span>
                </div>
                <h2 class="text-white font-bold text-lg mb-2">Absensi Siswa</h2>
                <p class="text-slate-400 text-sm leading-relaxed">Scan kartu / QR siswa beserta <span class="text-blue-300 font-medium">pilihan mata pelajaran</span></p>
                <div class="mt-5 flex items-center justify-center gap-2 text-blue-400 text-sm font-medium group-hover:gap-3 transition-all">
                    <span>Buka Scanner</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                    </svg>
                </div>
            </a>

            <!-- Scanner 2: Guru & Staf -->
            <a href="{{ route('scan.guru-staf') }}" id="btn-scanner-guru"
               class="card-hover group block bg-slate-800/50 backdrop-blur border border-emerald-500/30 rounded-3xl p-7 text-center hover:bg-emerald-600/10 hover:border-emerald-400/60 hover:shadow-2xl hover:shadow-emerald-500/20">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-emerald-600/20 border border-emerald-500/30 mb-5 group-hover:bg-emerald-600/30 transition-colors">
                    <svg class="w-9 h-9 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z"/>
                    </svg>
                </div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 mb-4">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span class="text-emerald-300 text-xs font-medium">Scanner 2</span>
                </div>
                <h2 class="text-white font-bold text-lg mb-2">Absensi Guru & Staf</h2>
                <p class="text-slate-400 text-sm leading-relaxed">Absen <span class="text-emerald-300 font-medium">masuk & pulang</span> untuk guru dan staf sekolah</p>
                <div class="mt-5 flex items-center justify-center gap-2 text-emerald-400 text-sm font-medium group-hover:gap-3 transition-all">
                    <span>Buka Scanner</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                    </svg>
                </div>
            </a>
        </div>

        <!-- Card: Absen Manual (Input tanpa QR) -->
        <a href="{{ route('scan.absen-manual.index') }}" id="btn-absen-manual"
           class="card-hover group block bg-slate-800/50 backdrop-blur border border-amber-500/30 rounded-3xl p-6 hover:bg-amber-600/10 hover:border-amber-400/60 hover:shadow-2xl hover:shadow-amber-500/20">
            <div class="flex items-center gap-5">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-amber-600/20 border border-amber-500/30 flex-shrink-0 group-hover:bg-amber-600/30 transition-colors">
                    <svg class="w-8 h-8 text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z"/>
                    </svg>
                </div>
                <div class="text-left flex-1">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/30 mb-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                        <span class="text-amber-300 text-xs font-medium">Input Manual</span>
                    </div>
                    <h2 class="text-white font-bold text-base mb-1">Absen Manual Siswa</h2>
                    <p class="text-slate-400 text-xs leading-relaxed">Input absensi <span class="text-amber-300 font-medium">tanpa QR code</span> — pilih kelas, tanggal & status siswa</p>
                </div>
                <svg class="w-5 h-5 text-amber-400/60 flex-shrink-0 group-hover:text-amber-400 group-hover:translate-x-1 transition-all" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                </svg>
            </div>
        </a>

        <!-- Admin link (hanya untuk admin/superadmin) -->
        @if(in_array(auth()->user()->role, ['superadmin','admin','tu','kepala_sekolah']))
        <div class="text-center mt-6">
            <a href="{{ route('admin.dashboard') }}" class="text-slate-500 hover:text-slate-300 text-xs transition-colors">
                ← Kembali ke Dashboard Admin
            </a>
        </div>
        @endif
    </div>

<script>
function tick() {
    const n = new Date();
    document.getElementById('clockDisplay').textContent =
        n.toLocaleDateString('id-ID', {weekday:'long',day:'numeric',month:'long',year:'numeric'}) +
        ' · ' + n.toLocaleTimeString('id-ID');
}
tick(); setInterval(tick, 1000);
</script>
</body>
</html>
