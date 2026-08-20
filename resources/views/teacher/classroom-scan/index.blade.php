<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Scan Absensi — SD N 30 Selayo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- html5-qrcode library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        #qr-reader { border: none !important; background: transparent !important; }
        #qr-reader video { border-radius: 12px; width: 100% !important; display: block; }
        #qr-reader img { display: none !important; }
        #qr-reader__scan_region { border-radius: 12px; overflow: hidden; }
        #qr-reader__dashboard { padding: 8px 0 0 0 !important; }
        #qr-reader__dashboard_section_csr button {
            background: #2563eb !important; color: white !important;
            border-radius: 8px !important; padding: 6px 16px !important;
            font-size: 12px !important; border: none !important;
        }
    </style>
</head>
<body class="h-full bg-slate-900 text-white" x-data="classroomScan()" x-init="init()">

{{-- HEADER --}}
<header class="bg-slate-800/80 backdrop-blur border-b border-slate-700/50 px-4 py-3 flex items-center justify-between sticky top-0 z-30">
    <div class="flex items-center gap-3">
        <a href="{{ route('teacher.sessions.index') }}" class="text-slate-400 hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
        </a>
        <div>
            <p class="text-white font-bold text-sm leading-tight">Scan Absensi</p>
            <p class="text-slate-400 text-xs" x-text="sessionInfo || 'SD Negeri 30 Selayo'"></p>
        </div>
    </div>
    <div class="text-right">
        <p class="text-white font-mono font-bold text-sm" x-text="clock"></p>
        <p class="text-slate-400 text-xs" x-text="dateStr"></p>
    </div>
</header>

<div class="max-w-2xl mx-auto px-4 py-5">

{{-- ══════════════════════════════════════════════════════
     FASE 1: SETUP — Pilih tanggal, mapel, mode
════════════════════════════════════════════════════════ --}}
<div x-show="phase===1" x-cloak x-transition>

    <h2 class="text-white font-bold text-lg mb-4">⚙️ Setup Sesi Absensi</h2>

    {{-- Tanggal --}}
    <div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-5 mb-4">
        <h3 class="text-white font-semibold text-sm mb-3">1. Tanggal</h3>
        <div>
            <label class="block text-xs text-slate-400 mb-1">Tanggal *</label>
            <input type="date" x-model="setup.date" :max="today"
                   class="w-full bg-slate-700/50 border border-slate-600/60 text-white rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
        </div>
    </div>

    {{-- Pilih Mata Pelajaran --}}
    <div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-5 mb-4">
        <h3 class="text-white font-semibold text-sm mb-1">2. Mata Pelajaran yang Diajarkan *</h3>
        <p class="text-slate-400 text-xs mb-3">Pilih satu atau lebih mapel hari ini.</p>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
            @foreach($subjects as $subject)
            <label class="cursor-pointer">
                <input type="checkbox" :value="{{ $subject->id }}" x-model="setup.subjectIds" class="sr-only peer">
                <div class="rounded-xl border-2 p-3 transition-all duration-200
                            border-slate-600/60 bg-slate-700/30 select-none
                            peer-checked:border-blue-500 peer-checked:bg-blue-500/10">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-bold px-1.5 py-0.5 rounded
                            {{ $subject->class_group === 'all' ? 'bg-slate-600 text-slate-200' : 'bg-indigo-500/20 text-indigo-300' }}">
                            {{ $subject->code }}
                        </span>
                        <svg class="w-4 h-4 text-blue-400 opacity-0 peer-checked:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="text-white text-xs font-semibold leading-tight">{{ $subject->name }}</p>
                </div>
            </label>
            @endforeach
        </div>
        <p class="text-xs text-slate-500 mt-2"><span x-text="setup.subjectIds.length"></span> mapel dipilih</p>
    </div>

    {{-- Input Mode --}}
    <div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-5 mb-5">
        <h3 class="text-white font-semibold text-sm mb-3">3. Mode Scan</h3>
        <div class="grid grid-cols-2 gap-3">
            <label class="cursor-pointer">
                <input type="radio" x-model="setup.mode" value="qr" class="sr-only peer">
                <div class="rounded-xl border-2 p-4 text-center transition-all
                            border-slate-600/60 bg-slate-700/30
                            peer-checked:border-emerald-500 peer-checked:bg-emerald-500/10">
                    <div class="text-3xl mb-1">📷</div>
                    <p class="text-white text-sm font-semibold">Kamera QR</p>
                    <p class="text-slate-400 text-xs mt-0.5">Scan pakai kamera HP/laptop</p>
                </div>
            </label>
            <label class="cursor-pointer">
                <input type="radio" x-model="setup.mode" value="rfid" class="sr-only peer">
                <div class="rounded-xl border-2 p-4 text-center transition-all
                            border-slate-600/60 bg-slate-700/30
                            peer-checked:border-purple-500 peer-checked:bg-purple-500/10">
                    <div class="text-3xl mb-1">🪪</div>
                    <p class="text-white text-sm font-semibold">RFID Reader</p>
                    <p class="text-slate-400 text-xs mt-0.5">Tempelkan kartu RFID</p>
                </div>
            </label>
        </div>
    </div>

    <button @click="startSession()"
            :disabled="!setup.date || setup.subjectIds.length===0"
            class="w-full py-3.5 rounded-2xl font-bold text-base transition-all shadow-lg
                   bg-blue-600 hover:bg-blue-500 disabled:opacity-40 disabled:cursor-not-allowed
                   shadow-blue-500/20 text-white">
        🚀 Mulai Scan Absensi
    </button>
</div>

{{-- ══════════════════════════════════════════════════════
     FASE 2: SCAN — QR camera atau RFID input
════════════════════════════════════════════════════════ --}}
<div x-show="phase===2" x-cloak x-transition>

    {{-- Stats bar --}}
    <div class="grid grid-cols-2 gap-3 mb-4">
        <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-3 text-center">
            <div class="text-2xl font-bold text-emerald-300" x-text="scannedStudents.length"></div>
            <div class="text-xs text-emerald-400 mt-0.5">Hadir Terscanned</div>
        </div>
        <div class="bg-slate-700/50 border border-slate-600/40 rounded-xl p-3 text-center">
            <div class="text-2xl font-bold text-slate-300" x-text="Object.keys(sessionMap).length"></div>
            <div class="text-xs text-slate-400 mt-0.5">Kelas Terlibat</div>
        </div>
    </div>

    {{-- Toast feedback --}}
    <div x-show="toast.msg" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="mb-3 p-4 rounded-2xl border flex items-center gap-3"
         :class="{
            'bg-emerald-500/10 border-emerald-500/30': toast.type==='success',
            'bg-red-500/10 border-red-500/30': toast.type==='error',
            'bg-amber-500/10 border-amber-500/30': toast.type==='warn',
         }">
        <span class="text-2xl flex-shrink-0" x-text="toast.icon"></span>
        <div>
            <p class="font-semibold text-sm"
               :class="{'text-emerald-300':toast.type==='success','text-red-300':toast.type==='error','text-amber-300':toast.type==='warn'}"
               x-text="toast.title"></p>
            <p class="text-xs text-slate-400" x-text="toast.msg"></p>
        </div>
    </div>

    {{-- QR Camera Mode --}}
    <div x-show="setup.mode==='qr'" class="mb-4">
        <div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-700/50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full" :class="camActive ? 'bg-emerald-400 animate-pulse' : 'bg-amber-400'"></span>
                    <span class="text-sm text-white font-medium" x-text="camActive ? 'Kamera QR Aktif' : (camError ? 'Kamera Error' : 'Menyiapkan Kamera...')"></span>
                </div>
                <button x-show="camError" @click="startQr()" class="text-xs bg-blue-600 hover:bg-blue-500 text-white px-3 py-1 rounded-lg transition-colors">🔄 Coba Ulang</button>
            </div>

            {{-- Placeholder saat kamera belum aktif --}}
            <div x-show="!camActive && !camError" class="flex flex-col items-center justify-center py-10 px-6 text-center">
                <div class="w-14 h-14 rounded-full bg-amber-500/10 border border-amber-500/20 flex items-center justify-center mb-3 animate-pulse">
                    <svg class="w-7 h-7 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                    </svg>
                </div>
                <p class="text-amber-300 font-semibold text-sm mb-1">Menunggu Izin Kamera</p>
                <p class="text-slate-500 text-xs">Jika tidak ada popup, tekan tombol di bawah ini</p>
                <button @click="startQr()" class="mt-3 text-sm bg-blue-600 hover:bg-blue-500 text-white px-5 py-2 rounded-xl transition-colors font-semibold">📷 Aktifkan Kamera</button>
            </div>

            {{-- Pesan error --}}
            <div x-show="camError" class="flex flex-col items-center justify-center py-8 px-6 text-center">
                <div class="w-12 h-12 rounded-full bg-red-500/10 border border-red-500/20 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                </div>
                <p class="text-red-300 font-semibold text-sm mb-1">Akses Kamera Gagal</p>
                <p class="text-slate-400 text-xs leading-relaxed" x-text="camError"></p>
            </div>

            <div x-show="camActive" class="p-3">
                <p class="text-center text-xs text-slate-500 mb-2">Arahkan QR Code ke dalam kotak. Scan otomatis.</p>
            </div>
            <div class="px-3 pb-3" :class="camActive ? '' : 'hidden'">
                <div id="qr-reader" class="w-full rounded-xl overflow-hidden"></div>
            </div>
        </div>
    </div>

    {{-- RFID Mode --}}
    <div x-show="setup.mode==='rfid'" class="mb-4">
        <div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-xl bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-2xl">🪪</div>
                <div>
                    <p class="text-white font-semibold">RFID Reader Aktif</p>
                    <p class="text-slate-400 text-xs">Tempelkan kartu RFID atau ketik kode siswa</p>
                </div>
            </div>
            <div class="relative">
                <input type="text" id="rfidInput"
                       class="w-full bg-slate-700/50 border-2 border-purple-500/40 focus:border-purple-400 text-white rounded-xl px-4 py-3 text-sm font-mono focus:outline-none focus:ring-0 transition-all"
                       placeholder="Tempelkan kartu atau ketik kode..."
                       @keydown="onRfidKey($event)"
                       autocomplete="off" spellcheck="false">
                <div class="absolute right-3 top-1/2 -translate-y-1/2">
                    <svg class="w-5 h-5 text-purple-400 animate-pulse" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 0 1-3-3m3 3a3 3 0 0 0 3 3h7.5a3 3 0 0 0 3-3m-13.5 0v-6a3 3 0 0 1 3-3h7.5a3 3 0 0 1 3 3v6"/></svg>
                </div>
            </div>
            <p class="text-slate-500 text-xs mt-2">Kode akan otomatis terkirim saat terbaca reader.</p>
        </div>
    </div>

    {{-- Daftar Siswa yang Sudah Scan (Real-time) --}}
    <div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl overflow-hidden mb-4">
        <div class="px-4 py-3 border-b border-slate-700/50 flex items-center justify-between">
            <h3 class="text-white font-semibold text-sm">✅ Siswa Hadir</h3>
            <span class="text-slate-400 text-xs" x-text="scannedStudents.length + ' siswa'"></span>
        </div>
        <div x-show="scannedStudents.length === 0" class="px-4 py-8 text-center text-slate-500 text-sm">
            Belum ada siswa yang scan...
        </div>
        <div class="divide-y divide-slate-700/30 max-h-72 overflow-y-auto">
            <template x-for="s in [...scannedStudents].reverse()" :key="s.id">
                <div class="flex items-center gap-3 px-4 py-3 bg-emerald-500/5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-emerald-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-white text-sm font-medium" x-text="s.name"></p>
                        <p class="text-xs text-slate-500" x-text="s.classroom_name + ' · NIS: ' + s.nis"></p>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-md bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 font-medium flex-shrink-0">Hadir</span>
                </div>
            </template>
        </div>
    </div>

    {{-- Action: Selesai --}}
    <div class="flex gap-3">
        <button @click="phase=1; stopQr()"
                class="flex-1 py-3 rounded-xl bg-slate-700/60 text-slate-300 text-sm font-semibold hover:bg-slate-600/60 transition-all">
            ← Setup Ulang
        </button>
        <button @click="goToReview()" :disabled="scannedStudents.length===0 || loading"
                class="flex-2 flex-grow-[2] py-3 rounded-xl bg-amber-600 hover:bg-amber-500 disabled:opacity-40 text-white text-sm font-bold transition-all shadow-lg shadow-amber-500/20">
            <span x-show="!loading">Selesai Scan →</span>
            <span x-show="loading" class="flex items-center justify-center gap-2">
                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Memuat...
            </span>
        </button>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     FASE 3: REVIEW — Isi status siswa yang belum hadir (per kelas)
════════════════════════════════════════════════════════ --}}
<div x-show="phase===3" x-cloak x-transition>
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-white font-bold text-lg">📝 Review Kehadiran</h2>
        <span class="text-slate-400 text-sm" x-text="totalNotScanned + ' siswa belum scan'"></span>
    </div>

    {{-- Per kelas / per sesi --}}
    <template x-for="sess in reviewSessions" :key="sess.session_id">
        <div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl overflow-hidden mb-4">

            {{-- Header kelas --}}
            <div class="px-4 py-3 border-b border-slate-700/50 bg-slate-700/30">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white font-semibold text-sm" x-text="sess.classroom_name"></p>
                        <p class="text-slate-400 text-xs">
                            <span x-text="sess.scanned_ids.length"></span> hadir ·
                            <span x-text="sess.students.length - sess.scanned_ids.length"></span> belum scan
                        </p>
                    </div>
                    {{-- Tombol set semua alpha/sakit/izin --}}
                    <div class="flex gap-1.5 flex-wrap justify-end">
                        @foreach(['Tanpa Keterangan' => 'red', 'Sakit' => 'amber', 'Izin' => 'blue'] as $status => $color)
                        <button type="button" @click="setAllNotScanned(sess, '{{ $status }}')"
                                class="text-xs px-2 py-1 rounded-lg border border-{{ $color }}-500/30 text-{{ $color }}-400 hover:bg-{{ $color }}-500/10 transition-all">
                            {{ $status === 'Tanpa Keterangan' ? 'Semua Alpha' : 'Semua '.$status }}
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Siswa hadir di kelas ini --}}
            <div x-show="sess.scanned_ids.length > 0" class="px-4 py-2.5 border-b border-slate-700/30">
                <p class="text-emerald-400 text-xs font-semibold mb-1.5">✅ Hadir (<span x-text="sess.scanned_ids.length"></span>)</p>
                <div class="flex flex-wrap gap-1.5">
                    <template x-for="sid in sess.scanned_ids" :key="sid">
                        <span class="inline-flex px-2 py-0.5 rounded-md bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-xs"
                              x-text="getStudentName(sess, sid)"></span>
                    </template>
                </div>
            </div>

            {{-- Siswa belum scan — pilih status --}}
            <div class="divide-y divide-slate-700/30">
                <template x-for="student in getNotScanned(sess)" :key="student.id">
                    <div class="flex items-center gap-3 px-4 py-3 hover:bg-slate-700/20 transition-colors">
                        <div class="flex-1 min-w-0">
                            <p class="text-white text-sm font-medium" x-text="student.name"></p>
                            <p class="text-slate-500 text-xs" x-text="'NIS: ' + student.nis"></p>
                        </div>
                        <div class="flex gap-1.5 flex-shrink-0">
                            <template x-for="st in statuses" :key="st.id">
                                <label class="cursor-pointer">
                                    <input type="radio" :name="'status_' + sess.session_id + '_' + student.id"
                                           :value="st.id"
                                           x-model="reviewStatus[sess.session_id + '_' + student.id]"
                                           class="sr-only peer">
                                    <span class="inline-flex px-2 py-1 rounded-lg text-xs font-semibold border cursor-pointer transition-all"
                                          :class="{
                                            'border-slate-600/40 text-slate-400 hover:border-red-500/50 hover:text-red-400 peer-checked:bg-red-500/15 peer-checked:border-red-500/50 peer-checked:text-red-300': st.name==='Tanpa Keterangan',
                                            'border-slate-600/40 text-slate-400 hover:border-amber-500/50 hover:text-amber-400 peer-checked:bg-amber-500/15 peer-checked:border-amber-500/50 peer-checked:text-amber-300': st.name==='Sakit',
                                            'border-slate-600/40 text-slate-400 hover:border-blue-500/50 hover:text-blue-400 peer-checked:bg-blue-500/15 peer-checked:border-blue-500/50 peer-checked:text-blue-300': st.name==='Izin',
                                          }"
                                          x-text="st.name === 'Tanpa Keterangan' ? 'Alpha' : st.name"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Jika semua sudah scan --}}
                <template x-if="getNotScanned(sess).length === 0">
                    <div class="px-4 py-4 text-center text-slate-500 text-xs">🎉 Semua siswa di kelas ini sudah scan</div>
                </template>
            </div>
        </div>
    </template>

    {{-- Catatan --}}
    <div class="mb-5">
        <label class="block text-xs text-slate-400 mb-1.5">Catatan sesi (opsional)</label>
        <textarea x-model="notes" rows="2"
                  class="w-full bg-slate-800/50 border border-slate-700/50 text-white placeholder-slate-600 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                  placeholder="Keterangan tambahan..."></textarea>
    </div>

    <div class="flex gap-3">
        <button @click="phase=2; if(setup.mode==='qr') startQr(); else focusRfid();"
                class="px-5 py-3 rounded-xl bg-slate-700/60 text-slate-300 text-sm font-semibold hover:bg-slate-600/60 transition-all">
            ← Scan Lagi
        </button>
        <button @click="finalize()" :disabled="loading"
                class="flex-1 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 disabled:opacity-40 text-white font-bold text-sm transition-all shadow-lg shadow-emerald-500/20">
            <span x-show="!loading">💾 Simpan Absensi</span>
            <span x-show="loading" class="flex items-center justify-center gap-2">
                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Menyimpan...
            </span>
        </button>
    </div>
</div>

</div>{{-- end max-w container --}}

<script>
function classroomScan() {
    return {
        phase: 1,
        loading: false,
        today: '{{ $today }}',
        clock: '', dateStr: '',

        // Setup (Fase 1)
        setup: {
            date: '{{ $today }}',
            subjectIds: [],
            mode: 'qr',
        },

        // Fase 2: tracking scan
        scannedStudents: [],  // [{id, name, nis, classroom_name, session_id}]
        sessionMap: {},       // { session_id: classroom_name }

        // Fase 3: review per kelas
        reviewSessions: [],   // data dari API review
        reviewStatus: {},     // { 'sessionId_studentId': statusId }
        notes: '',

        // Header info
        sessionInfo: '',

        // Statuses
        statuses: @json($attendanceStatuses),

        // Toast
        toast: { type:'', title:'', msg:'', icon:'' },
        toastTimer: null,

        // QR
        qrScanner: null,
        camActive: false,
        camError: '',

        // RFID buffer
        rfidBuf: '',
        rfidTimer: null,

        init() {
            this.tickClock();
            setInterval(() => this.tickClock(), 1000);
        },

        tickClock() {
            const n = new Date();
            this.clock   = n.toLocaleTimeString('id-ID', {hour:'2-digit',minute:'2-digit',second:'2-digit'});
            this.dateStr = n.toLocaleDateString('id-ID', {day:'numeric',month:'long',year:'numeric'});
        },

        // ── FASE 1 → 2 : Tidak perlu API, langsung mulai scan ──────────────
        startSession() {
            if (!this.setup.date || this.setup.subjectIds.length === 0) return;
            this.scannedStudents = [];
            this.sessionMap      = {};
            this.reviewSessions  = [];
            this.reviewStatus    = {};
            const tgl = new Date(this.setup.date).toLocaleDateString('id-ID', {day:'numeric',month:'short',year:'numeric'});
            this.sessionInfo = tgl;
            this.phase = 2;
            this.$nextTick(() => {
                if (this.setup.mode === 'qr') this.startQr();
                else this.focusRfid();
            });
        },

        // ── QR Scanner ──────────────────────────────────────────────────────
        async startQr() {
            this.camError  = '';
            this.camActive = false;
            if (window.isSecureContext === false) {
                this.camError = 'Kamera hanya bisa diakses via HTTPS atau localhost.';
                return;
            }
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                stream.getTracks().forEach(t => t.stop());
            } catch (err) {
                if (err.name === 'NotAllowedError') this.camError = 'Akses kamera ditolak.';
                else if (err.name === 'NotFoundError') this.camError = 'Kamera tidak ditemukan.';
                else this.camError = 'Error: ' + err.message;
                return;
            }
            if (this.qrScanner) { this.qrScanner.clear(); this.qrScanner = null; }
            this.qrScanner = new Html5Qrcode('qr-reader');
            this.qrScanner.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                (code) => this.processScan(code),
                () => {}
            ).then(() => {
                this.camActive = true;
            }).catch(e => {
                this.camError = 'Kamera gagal dimulai: ' + e;
            });
        },

        stopQr() {
            if (this.qrScanner) {
                this.qrScanner.stop().catch(() => {});
                this.qrScanner.clear();
                this.qrScanner = null;
            }
            this.camActive = false;
        },

        // ── RFID Input ──────────────────────────────────────────────────────
        focusRfid() {
            this.$nextTick(() => document.getElementById('rfidInput')?.focus());
        },

        onRfidKey(e) {
            if (e.key === 'Enter') {
                clearTimeout(this.rfidTimer);
                const code = this.rfidBuf.trim();
                this.rfidBuf = '';
                document.getElementById('rfidInput').value = '';
                if (code) this.processScan(code);
                e.preventDefault(); return;
            }
            if (e.key.length === 1) this.rfidBuf += e.key;
            clearTimeout(this.rfidTimer);
            this.rfidTimer = setTimeout(() => {
                if (this.rfidBuf.trim()) this.processScan(this.rfidBuf.trim());
                this.rfidBuf = '';
                if (document.getElementById('rfidInput')) document.getElementById('rfidInput').value = '';
            }, 300);
        },

        // ── Proses scan — auto-detect kelas dari data siswa ─────────────────
        async processScan(code) {
            if (this.loading) return;
            try {
                const res = await this.post('{{ route('teacher.classroom-scan.scan') }}', {
                    scan_code:   code,
                    date:        this.setup.date,
                    subject_ids: this.setup.subjectIds,
                });
                const data = await res.json();

                if (data.success) {
                    // Cek duplikat di frontend
                    if (!this.scannedStudents.find(s => s.id === data.student.id)) {
                        this.scannedStudents.push({
                            id:             data.student.id,
                            name:           data.student.name,
                            nis:            data.student.nis,
                            classroom_name: data.classroom_name,
                            session_id:     data.session_id,
                        });
                    }
                    // Simpan session_id → classroom_name
                    if (!this.sessionMap[data.session_id]) {
                        this.sessionMap[data.session_id] = data.classroom_name;
                    }
                    this.showToast('success', '✅ Hadir!', data.student.name + ' · ' + data.classroom_name, '✅');
                    this.beep(880);
                } else {
                    const icon = { 'ALREADY_SCANNED': '🔁', 'NOT_FOUND': '❓', 'NO_CLASS': '🏫' }[data.code] ?? '❌';
                    this.showToast('warn', data.code === 'ALREADY_SCANNED' ? 'Sudah Absen' : 'Ditolak', data.message, icon);
                    this.beep(300);
                }
                if (this.setup.mode === 'rfid') {
                    setTimeout(() => this.focusRfid(), 100);
                }
            } catch(e) {
                this.showToast('error', 'Error', 'Gagal koneksi server', '🌐');
            }
        },

        // ── FASE 2 → 3 : Muat data review dari server ───────────────────────
        async goToReview() {
            this.stopQr();
            const sessionIds = Object.keys(this.sessionMap).map(Number);
            if (sessionIds.length === 0) {
                this.phase = 3;
                return;
            }
            this.loading = true;
            try {
                const res  = await this.post('{{ route('teacher.classroom-scan.review') }}', {
                    session_ids: sessionIds,
                });
                const data = await res.json();
                if (data.success) {
                    const alphaId = @json($alphaId);
                    this.reviewSessions = data.sessions;
                    // Set default status: Tanpa Keterangan untuk semua yang belum scan
                    data.sessions.forEach(sess => {
                        sess.students.forEach(s => {
                            if (!sess.scanned_ids.includes(s.id)) {
                                this.reviewStatus[sess.session_id + '_' + s.id] = String(alphaId);
                            }
                        });
                    });
                    this.phase = 3;
                } else {
                    this.showToast('error', 'Gagal', 'Gagal memuat data review', '❌');
                }
            } catch(e) {
                this.showToast('error', 'Error', 'Gagal koneksi server', '🌐');
            } finally {
                this.loading = false;
            }
        },

        // ── Helpers review ───────────────────────────────────────────────────
        get totalNotScanned() {
            return this.reviewSessions.reduce((sum, sess) => {
                return sum + sess.students.filter(s => !sess.scanned_ids.includes(s.id)).length;
            }, 0);
        },

        getNotScanned(sess) {
            return sess.students.filter(s => !sess.scanned_ids.includes(s.id));
        },

        getStudentName(sess, id) {
            return sess.students.find(s => s.id === id)?.name ?? '';
        },

        setAllNotScanned(sess, statusName) {
            const st = this.statuses.find(s => s.name === statusName);
            if (!st) return;
            this.getNotScanned(sess).forEach(s => {
                this.reviewStatus[sess.session_id + '_' + s.id] = String(st.id);
            });
        },

        // ── FASE 3 → Simpan ─────────────────────────────────────────────────
        async finalize() {
            this.loading = true;
            const haidirId = @json($haidirId);
            const alphaId  = @json($alphaId);

            const attendances = [];

            this.reviewSessions.forEach(sess => {
                // Siswa yang hadir
                sess.scanned_ids.forEach(id => {
                    attendances.push({ session_id: sess.session_id, student_id: id, status_id: haidirId });
                });
                // Siswa yang belum hadir
                this.getNotScanned(sess).forEach(s => {
                    attendances.push({
                        session_id: sess.session_id,
                        student_id: s.id,
                        status_id:  Number(this.reviewStatus[sess.session_id + '_' + s.id] ?? alphaId),
                    });
                });
            });

            try {
                const res = await this.post('{{ route('teacher.classroom-scan.finalize') }}', {
                    attendances,
                    notes: this.notes,
                });
                const data = await res.json();
                if (data.success) {
                    window.location.href = data.redirect_url;
                } else {
                    throw new Error(data.message || 'Gagal menyimpan');
                }
            } catch(e) {
                this.showToast('error', 'Gagal Simpan', e.message, '❌');
                this.loading = false;
            }
        },

        // ── Helpers ─────────────────────────────────────────────────────────
        async post(url, data) {
            return fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify(data),
            });
        },

        showToast(type, title, msg, icon) {
            clearTimeout(this.toastTimer);
            this.toast = { type, title, msg, icon };
            this.toastTimer = setTimeout(() => {
                this.toast = { type:'', title:'', msg:'', icon:'' };
            }, 5000);
        },

        beep(freq) {
            try {
                const c = new (window.AudioContext || window.webkitAudioContext)();
                const o = c.createOscillator(), g = c.createGain();
                o.connect(g); g.connect(c.destination);
                o.frequency.value = freq; o.type = 'sine';
                g.gain.setValueAtTime(0.15, c.currentTime);
                g.gain.exponentialRampToValueAtTime(0.001, c.currentTime + 0.3);
                o.start(); o.stop(c.currentTime + 0.3);
            } catch(_) {}
        },
    }
}
</script>
</body>
</html>
