<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Scanner Guru & Staf — SD Negeri 30 Selayo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        
        #qr-reader { border: none !important; background: transparent !important; }
        #qr-reader video { border-radius: 24px; width: 100% !important; display: block; object-fit: cover; height: 100%; }
        #qr-reader img { display: none !important; }
        #qr-reader__scan_region { border-radius: 24px; overflow: hidden; height: 100%; }
        #qr-reader__dashboard { padding: 8px 0 0 0 !important; position: absolute; bottom: 10px; width: 100%; z-index: 20; }
        #qr-reader__dashboard_section_csr button { 
            background: #2563eb !important; color: white !important; 
            border-radius: 8px !important; padding: 6px 16px !important;
            font-size: 12px !important; border: none !important;
        }

        @keyframes scanLine {
            0%   { top: 10%; opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 1; }
            100% { top: 90%; opacity: 0; }
        }
        .scan-line { animation: scanLine 2s ease-in-out infinite; }
        @keyframes pulse-ring {
            0%   { transform: scale(1); opacity: 0.6; }
            100% { transform: scale(1.4); opacity: 0; }
        }
        .pulse-ring { animation: pulse-ring 1.2s ease-out infinite; }
    </style>
</head>
<body class="h-full bg-gradient-to-br from-slate-900 via-emerald-950 to-slate-900 font-['Inter'] overflow-hidden"
      x-data="scannerApp()" x-init="init()">

    <!-- BG Blobs -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-32 -right-32 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 -left-32 w-80 h-80 bg-teal-500/10 rounded-full blur-3xl"></div>
    </div>

    <div class="relative h-full flex flex-col items-center justify-between max-w-sm mx-auto px-4 py-5">

        <!-- HEADER -->
        <header class="w-full text-center">
            <div class="flex items-center justify-center gap-3 mb-1">
                <a href="{{ route('scan.index') }}" class="w-8 h-8 rounded-lg bg-slate-700/60 border border-slate-600/40 flex items-center justify-center text-slate-400 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                </a>
                <div class="w-10 h-10 rounded-xl bg-emerald-600/20 border border-emerald-500/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z"/>
                    </svg>
                </div>
                <div class="text-left flex-1">
                    <h1 class="text-white font-bold text-base leading-tight">Scanner Guru & Staf</h1>
                    <p class="text-emerald-300/60 text-xs">SD Negeri 30 Selayo</p>
                </div>
            </div>
            <div class="text-3xl font-mono font-bold text-white tabular-nums mt-2" x-text="clock"></div>
            <div class="text-slate-400 text-xs mt-0.5" x-text="dateStr"></div>
        </header>

        <!-- GEOFENCE -->
        <div class="w-full">
            <div class="rounded-2xl border backdrop-blur-xl p-3.5 transition-all duration-500"
                 :class="{
                   'bg-emerald-500/10 border-emerald-500/30': geo.status === 'inside',
                   'bg-red-500/10 border-red-500/30': geo.status === 'outside',
                   'bg-amber-500/10 border-amber-500/30': geo.status === 'loading',
                   'bg-slate-700/40 border-slate-600/40': geo.status === 'disabled',
                 }">
                <div class="flex items-center gap-3">
                    <div class="relative flex-shrink-0">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                             :class="{'bg-emerald-500/20':geo.status==='inside','bg-red-500/20':geo.status==='outside','bg-amber-500/20':geo.status==='loading','bg-slate-600/30':geo.status==='disabled'}">
                            <template x-if="geo.status==='loading'">
                                <svg class="w-6 h-6 text-amber-400 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            </template>
                            <template x-if="geo.status==='inside'">
                                <svg class="w-6 h-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                            </template>
                            <template x-if="geo.status==='outside'">
                                <svg class="w-6 h-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                            </template>
                            <template x-if="geo.status==='disabled'">
                                <svg class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                            </template>
                        </div>
                        <div x-show="geo.status==='outside'" class="absolute inset-0 rounded-xl border-2 border-red-400 pulse-ring"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold"
                           :class="{'text-emerald-300':geo.status==='inside','text-red-300':geo.status==='outside','text-amber-300':geo.status==='loading','text-slate-300':geo.status==='disabled'}"
                           x-text="geo.title"></p>
                        <p class="text-xs mt-0.5 truncate"
                           :class="{'text-emerald-400/70':geo.status==='inside','text-red-400/70':geo.status==='outside','text-amber-400/70':geo.status==='loading','text-slate-400/70':geo.status==='disabled'}"
                           x-text="geo.sub"></p>
                    </div>
                    <div x-show="geo.distance !== null" class="flex-shrink-0 text-right">
                        <div class="text-xl font-bold font-mono" :class="{'text-emerald-300':geo.status==='inside','text-red-300':geo.status==='outside'}" x-text="geo.distance+'m'"></div>
                        <div class="text-xs text-slate-500">dari sekolah</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ════ SCANNER AREA ════ -->
        <div class="w-full flex flex-col items-center">
            <!-- Mode Tabs -->
            <div class="flex bg-slate-800/60 border border-slate-700/40 rounded-2xl p-1 mb-4 w-full">
                <button @click="mode='masuk'" id="tab-masuk"
                        class="flex-1 py-2 rounded-xl text-sm font-semibold transition-all duration-300"
                        :class="mode==='masuk' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/20' : 'text-slate-400 hover:text-slate-200'">
                    ✅ Masuk
                </button>
                <button @click="mode='pulang'" id="tab-pulang"
                        class="flex-1 py-2 rounded-xl text-sm font-semibold transition-all duration-300"
                        :class="mode==='pulang' ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/20' : 'text-slate-400 hover:text-slate-200'">
                    🏠 Pulang
                </button>
            </div>

            <!-- Scanner Frame -->
            <div class="relative w-64 h-64">
                <div class="absolute inset-0 rounded-3xl bg-slate-800/30 border border-slate-600/30 backdrop-blur overflow-hidden">
                    <div class="absolute inset-x-6 h-0.5 scan-line rounded-full"
                         :class="mode==='masuk' ? 'bg-gradient-to-r from-transparent via-emerald-400 to-transparent' : 'bg-gradient-to-r from-transparent via-orange-400 to-transparent'"
                         x-show="(geo.status==='inside'||geo.status==='disabled') && !processing"></div>

                    <input type="text" id="rfidInput"
                           class="absolute opacity-0 inset-0 w-full h-full cursor-default z-30"
                           autocomplete="off" spellcheck="false"
                           @keydown="onKey($event)"
                           :disabled="processing || geo.status==='loading'">

                    <!-- Camera Container -->
                    <div id="qr-reader" class="absolute inset-0 w-full h-full z-0" x-show="camActive"></div>

                    <div class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center pointer-events-none z-10"
                         :class="camActive ? 'bg-slate-900/40 backdrop-blur-[2px]' : ''">
                        <div class="w-14 h-14 rounded-2xl mb-3 flex items-center justify-center"
                             :class="mode==='masuk' ? 'bg-emerald-500/20 border border-emerald-400/30' : 'bg-orange-500/20 border border-orange-400/30'">
                            <svg class="w-9 h-9" :class="mode==='masuk'?'text-emerald-400':'text-orange-400'"
                                 fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5Z"/>
                            </svg>
                        </div>
                        <p class="text-slate-200 text-xs font-medium leading-relaxed drop-shadow-md">
                            <span x-show="geo.status==='inside'||geo.status==='disabled'">Arahkan QR Code atau tempelkan kartu RFID</span>
                            <span x-show="geo.status==='loading'" class="text-amber-300">Menunggu sinyal GPS...</span>
                            <span x-show="geo.status==='outside'" class="text-red-300">Pindah ke area sekolah dahulu</span>
                        </p>
                    </div>

                    <button @click="toggleCamera()" class="absolute bottom-3 right-3 z-40 bg-slate-800/80 hover:bg-slate-700 text-white p-2 rounded-full backdrop-blur border border-slate-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </button>

                    <div x-show="processing" x-cloak
                         class="absolute inset-0 bg-slate-900/80 backdrop-blur flex flex-col items-center justify-center rounded-3xl">
                        <div class="w-8 h-8 border-2 border-emerald-400/30 border-t-emerald-400 rounded-full animate-spin mb-2"></div>
                        <p class="text-emerald-300 text-xs">Memproses...</p>
                    </div>
                </div>

                <!-- Corners -->
                <div class="absolute top-0 left-0 w-7 h-7 border-t-2 border-l-2 rounded-tl-2xl" :class="mode==='masuk'?'border-emerald-400':'border-orange-400'"></div>
                <div class="absolute top-0 right-0 w-7 h-7 border-t-2 border-r-2 rounded-tr-2xl" :class="mode==='masuk'?'border-emerald-400':'border-orange-400'"></div>
                <div class="absolute bottom-0 left-0 w-7 h-7 border-b-2 border-l-2 rounded-bl-2xl" :class="mode==='masuk'?'border-emerald-400':'border-orange-400'"></div>
                <div class="absolute bottom-0 right-0 w-7 h-7 border-b-2 border-r-2 rounded-br-2xl" :class="mode==='masuk'?'border-emerald-400':'border-orange-400'"></div>
            </div>
        </div>

        <!-- RESULT TOAST -->
        <div class="w-full min-h-[80px]">
            <div x-show="toast.title" x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 class="rounded-2xl border backdrop-blur overflow-hidden"
                 :class="{'bg-emerald-500/15 border-emerald-400/40':toast.type==='success','bg-red-500/15 border-red-400/40':toast.type==='error','bg-amber-500/15 border-amber-400/40':toast.type==='warn'}">
                <div x-show="toast.type==='success'" class="h-1 w-full bg-gradient-to-r from-emerald-400 to-teal-400"></div>
                <div class="p-4 flex items-start gap-3">
                    <span class="text-3xl leading-none" x-text="toast.icon"></span>
                    <div class="flex-1 min-w-0">
                        <p class="font-extrabold text-base leading-tight"
                           :class="{'text-emerald-200':toast.type==='success','text-red-200':toast.type==='error','text-amber-200':toast.type==='warn'}"
                           x-text="toast.title"></p>
                        <p class="text-xs text-slate-400 mt-1" x-show="toast.msg" x-text="toast.msg"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="text-center">
            <p class="text-slate-600 text-xs">
                Mode: <span class="font-semibold" :class="mode==='masuk'?'text-emerald-400':'text-orange-400'" x-text="mode.toUpperCase()"></span>
                · <a href="{{ route('scan.index') }}" class="hover:text-slate-400 transition-colors">Kembali</a>
                · <a href="{{ route('login') }}" class="hover:text-slate-400 transition-colors">Login</a>
            </p>
        </footer>
    </div>

<script>
function scannerApp() {
    return {
        mode: 'masuk',
        processing: false,
        geo: { status:'disabled', title:'Geofencing Nonaktif', sub:'Scan bebas tanpa batas lokasi', distance:null },
        toast: { type:'', title:'', msg:'', icon:'' },
        toastTimer: null,
        clock: '', dateStr: '',
        lat: 0, lon: 0,
        rfidBuf: '', rfidTimer: null,
        
        qrScanner: null,
        camActive: false,

        schoolLat: {{ $settings->school_latitude ?? -0.8175879 }},
        schoolLon: {{ $settings->school_longitude ?? 100.6331262 }},
        maxRadius: {{ $settings->geofence_radius_meters ?? 500 }},
        geoEnabled: false, // GPS dimatikan sementara

        init() {
            this.tickClock();
            setInterval(() => this.tickClock(), 1000);
            this.startGeo();
            this.$nextTick(() => document.getElementById('rfidInput')?.focus());
        },

        tickClock() {
            const n = new Date();
            this.clock   = n.toLocaleTimeString('id-ID', {hour:'2-digit',minute:'2-digit',second:'2-digit'});
            this.dateStr = n.toLocaleDateString('id-ID', {weekday:'long',day:'numeric',month:'long',year:'numeric'});
        },

        startGeo() {
            if (!this.geoEnabled) {
                this.geo = { status:'disabled', title:'Geofencing Nonaktif', sub:'Scan bebas tanpa batas lokasi', distance:null };
                document.getElementById('rfidInput')?.focus();
                return;
            }
            if (window.isSecureContext === false) {
                this.geo = { status:'outside', title:'Koneksi Tidak Aman', sub:'Browser menolak akses GPS via HTTP.', distance:null };
                return;
            }
            if (!navigator.geolocation) {
                this.geo = { status:'outside', title:'GPS Tidak Tersedia', sub:'Browser tidak mendukung Geolocation', distance:null };
                return;
            }
            navigator.geolocation.watchPosition(p => this.onGeoOk(p), e => this.onGeoErr(e),
                { enableHighAccuracy:true, timeout:15000, maximumAge:30000 });
        },

        onGeoOk(p) {
            this.lat = p.coords.latitude; this.lon = p.coords.longitude;
            const d = this.haversine(this.lat, this.lon, this.schoolLat, this.schoolLon);
            if (d <= this.maxRadius) {
                this.geo = { status:'inside', title:'✓ Di Dalam Area Sekolah', sub:`Akurasi ±${Math.round(p.coords.accuracy)}m · Siap scan!`, distance: Math.round(d) };
                document.getElementById('rfidInput')?.focus();
            } else {
                this.geo = { status:'outside', title:'⚠ Di Luar Area Sekolah', sub:'Dekati gedung sekolah untuk absen', distance: Math.round(d) };
            }
        },

        onGeoErr(e) {
            const m = { 1:['outside','Akses GPS Ditolak','Izinkan akses lokasi di pengaturan browser'],
                        2:['outside','Sinyal GPS Lemah','Pindah ke area terbuka'],
                        3:['loading','Timeout GPS','Sedang mencoba ulang...'] }[e.code] ?? ['outside','Error GPS',''];
            this.geo = { status:m[0], title:m[1], sub:m[2], distance:null };
        },

        onKey(e) {
            if (e.key === 'Enter') {
                clearTimeout(this.rfidTimer);
                const code = this.rfidBuf.trim();
                this.rfidBuf = '';
                if (code) this.submit(code);
                e.preventDefault(); return;
            }
            if (e.key.length === 1) this.rfidBuf += e.key;
            clearTimeout(this.rfidTimer);
            this.rfidTimer = setTimeout(() => { this.rfidBuf = ''; }, 500);
        },

        async toggleCamera() {
            if (this.camActive) {
                this.stopQr();
                return;
            }
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                stream.getTracks().forEach(t => t.stop());
            } catch (err) {
                this.showToast('error', '📷 Kamera Ditolak', 'Izinkan akses kamera di pengaturan browser, lalu refresh halaman.', '❌');
                return;
            }

            if (this.qrScanner) {
                try { await this.qrScanner.stop(); } catch(_) {}
                try { this.qrScanner.clear(); } catch(_) {}
                this.qrScanner = null;
            }

            this.qrScanner = new Html5Qrcode('qr-reader');
            const self = this; // referensi eksplisit agar aman di dalam callback

            this.qrScanner.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 180, height: 180 } },
                function onQrSuccess(decodedText) {
                    // Hanya proses sekali (cek camActive)
                    if (!self.camActive) return;
                    const code = decodedText.trim();
                    self.camActive = false;
                    if (self.qrScanner) {
                        self.qrScanner.stop()
                            .catch(() => {})
                            .finally(() => {
                                try { self.qrScanner && self.qrScanner.clear(); } catch(_) {}
                                self.qrScanner = null;
                                const inp = document.getElementById('rfidInput');
                                if (inp) { inp.classList.add('z-30'); inp.classList.remove('z-0'); inp.focus(); }
                            });
                    }
                    // Submit langsung tanpa menunggu stop() selesai
                    self.submit(code);
                },
                function onQrError() { /* abaikan error frame */ }
            ).then(() => {
                self.camActive = true;
                const inp = document.getElementById('rfidInput');
                if (inp) { inp.classList.remove('z-30'); inp.classList.add('z-0'); inp.focus(); }
            }).catch(e => {
                self.showToast('error', '📷 Kamera Error', e.message || 'Gagal membuka kamera', '❌');
            });
        },

        stopQr() {
            this.camActive = false;
            if (this.qrScanner) {
                this.qrScanner.stop().catch(() => {});
                try { this.qrScanner.clear(); } catch(_) {}
                this.qrScanner = null;
            }
            const inp = document.getElementById('rfidInput');
            if (inp) { inp.classList.add('z-30'); inp.classList.remove('z-0'); inp.focus(); }
        },

        async submit(code) {
            if (!code || !code.trim()) return;
            code = code.trim();
            if (this.processing) return;

            if (this.geoEnabled && this.geo.status === 'loading') {
                this.showToast('warn', '⏳ GPS Belum Siap', 'Tunggu GPS terdeteksi lalu scan ulang', '⏳');
                return;
            }

            this.processing = true;
            try {
                const csrfEl = document.querySelector('meta[name="csrf-token"]');
                const url = this.mode === 'masuk' ? '/scan/check-in' : '/scan/check-out';
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfEl ? csrfEl.content : '',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ scan_code: code, latitude: this.lat, longitude: this.lon }),
                });

                const text = await res.text();
                let data;
                try { data = JSON.parse(text); }
                catch(e) {
                    this.showToast('error', '❌ Error Server', 'Response tidak valid (Status ' + res.status + ')', '⚙️');
                    return;
                }

                if (data.success) {
                    const d = data.data;
                    const modeLabel = this.mode === 'masuk' ? 'Absen Masuk' : 'Absen Pulang';
                    this.showToast('success', '✅ ' + d.name, modeLabel + ' berhasil · Pukul ' + d.time, '✅');
                    this.beep(880);
                } else {
                    const icons = { GEOFENCE_VIOLATION:'📍', ALREADY_CHECKED_IN:'🔁', INVALID_CODE:'❓', SERVER_ERROR:'⚙️' };
                    this.showToast('error', (icons[data.code]??'❌') + ' ' + (data.code==='ALREADY_CHECKED_IN'?'Sudah Absen':data.code==='INVALID_CODE'?'Kode Tidak Terdaftar':'Ditolak'), data.message, icons[data.code]??'❌');
                    this.beep(280);
                }
            } catch(err) {
                this.showToast('error', '❌ Koneksi Gagal', err.message || 'Periksa koneksi internet', '🌐');
            } finally {
                this.processing = false;
                this.$nextTick(() => document.getElementById('rfidInput')?.focus());
            }
        },

        showToast(type, title, msg, icon) {
            clearTimeout(this.toastTimer);
            this.toast = { type, title, msg, icon };
            this.toastTimer = setTimeout(() => { this.toast = { type:'', title:'', msg:'', icon:'' }; }, 6000);
        },

        haversine(la1,lo1,la2,lo2) {
            const R=6371000, φ1=la1*Math.PI/180, φ2=la2*Math.PI/180,
                  Δφ=(la2-la1)*Math.PI/180, Δλ=(lo2-lo1)*Math.PI/180;
            const a=Math.sin(Δφ/2)**2+Math.cos(φ1)*Math.cos(φ2)*Math.sin(Δλ/2)**2;
            return R*2*Math.atan2(Math.sqrt(a),Math.sqrt(1-a));
        },

        beep(freq) {
            try {
                const c=new(window.AudioContext||window.webkitAudioContext)(),o=c.createOscillator(),g=c.createGain();
                o.connect(g);g.connect(c.destination);o.frequency.value=freq;o.type='sine';
                g.gain.setValueAtTime(0.2,c.currentTime);g.gain.exponentialRampToValueAtTime(0.001,c.currentTime+0.4);
                o.start();o.stop(c.currentTime+0.4);
            } catch(_){}
        },
    }
}
</script>
</body>
</html>
