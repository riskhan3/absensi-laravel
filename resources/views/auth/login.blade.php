<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Smart Absensi SDN 30 Salayo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak]{display:none!important}
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
        @keyframes pulse-glow { 0%,100%{box-shadow:0 0 20px 4px rgba(132,204,22,0.25)} 50%{box-shadow:0 0 35px 10px rgba(132,204,22,0.45)} }
        .logo-float { animation: float 3.5s ease-in-out infinite; }
        .logo-glow  { animation: pulse-glow 3.5s ease-in-out infinite; }
    </style>
</head>
<body class="h-full flex items-center justify-center font-['Inter'] p-4" style="background:linear-gradient(135deg,#0a0f05 0%,#0f1509 40%,#1a2310 70%,#0a0f05 100%)">

    <!-- Decorative blobs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-48 -right-48 w-96 h-96 rounded-full blur-3xl" style="background:rgba(132,204,22,0.08)"></div>
        <div class="absolute -bottom-48 -left-48 w-96 h-96 rounded-full blur-3xl" style="background:rgba(234,179,8,0.06)"></div>
    </div>

    <div class="relative w-full max-w-sm" x-data="{ loading: false, showPass: false }">
        <!-- Card -->
        <div class="backdrop-blur-xl rounded-3xl p-8 shadow-2xl" style="background:rgba(26,35,16,0.7);border:1px solid rgba(132,204,22,0.2)">

            <!-- Logo + Title -->
            <div class="text-center mb-8">
                <div class="logo-float logo-glow w-24 h-24 rounded-2xl mx-auto mb-4 overflow-hidden"
                            style="ring:2px solid rgba(132,204,22,0.5)">
                    <img src="{{ asset('images/logo.png') }}" alt="Smart Absensi"
                         class="w-full h-full object-cover">
                </div>
                <h1 class="text-2xl font-bold text-white">Smart Absensi</h1>
                <p style="color:rgba(163,230,53,0.6)" class="text-sm mt-1">SDN 30 Salayo &middot; Selamat Datang</p>
            </div>

            <!-- Error -->
            @if($errors->any())
            <div class="mb-5 p-3.5 rounded-xl bg-red-500/10 border border-red-500/30 text-red-300 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
                {{ $errors->first('email') }}
            </div>
            @endif

            <!-- Form -->
            <form action="{{ route('login.submit') }}" method="POST" @submit="loading = true">
                @csrf
                <div class="space-y-4">
                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full text-white placeholder-lime-200/20 rounded-xl px-4 py-2.5 text-sm focus:outline-none transition-all"
                               style="background:rgba(132,204,22,0.08);border:1px solid rgba(132,204,22,0.2);"
                               placeholder="admin@sekolah.com">
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">Password</label>
                        <div class="relative">
                            <input type="password" name="password" id="passwordInput" required
                                   class="w-full text-white placeholder-lime-200/20 rounded-xl px-4 py-2.5 pr-11 text-sm focus:outline-none transition-all"
                                   style="background:rgba(132,204,22,0.08);border:1px solid rgba(132,204,22,0.2);"
                                   placeholder="••••••••">
                            <button type="button" onclick="var p=document.getElementById('passwordInput');p.type=p.type==='password'?'text':'password';"
                                    class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-200 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember -->
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded border-lime-600 bg-lime-900/30 text-lime-500 focus:ring-lime-500/30">
                        <label for="remember" class="text-sm text-slate-400">Ingat saya</label>
                    </div>
                </div>

                <button type="submit"
                        class="w-full mt-6 font-semibold py-2.5 rounded-xl transition-all duration-200 text-sm flex items-center justify-center gap-2 disabled:opacity-70"
                        style="background:linear-gradient(135deg,#65a30d,#84cc16);color:#fff;box-shadow:0 4px 20px rgba(132,204,22,0.3)"
                        :disabled="loading">
                    <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <span x-text="loading ? 'Masuk...' : 'Masuk'">Masuk</span>
                </button>
            </form>

            <!-- Scanner link -->
            <div class="mt-6 text-center">
                <a href="{{ route('scan.index') }}" class="text-xs transition-colors flex items-center justify-center gap-1.5" style="color:rgba(163,230,53,0.5)" onmouseover="this.style.color='#84cc16'" onmouseout="this.style.color='rgba(163,230,53,0.5)'">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5Z"/></svg>
                    Langsung ke halaman Scanner
                </a>
            </div>
        </div>

        <p class="text-center text-xs mt-4" style="color:rgba(132,204,22,0.3)">&copy; {{ date('Y') }} Smart Absensi &mdash; SDN 30 Salayo</p>
    </div>
</body>
</html>
