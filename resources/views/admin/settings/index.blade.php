@extends('layouts.app')

@section('title', 'Pengaturan Sistem')
@section('page-title', 'Pengaturan Sistem')

@section('content')
<form action="{{ route('admin.settings.update') }}" method="POST" id="settingsForm">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- ─── Left Column ─── --}}
        <div class="xl:col-span-2 space-y-6">

            {{-- 1. Informasi Sekolah --}}
            <div class="bg-slate-800/60 border border-slate-700/50 rounded-2xl overflow-hidden backdrop-blur">
                <div class="px-6 py-4 border-b border-slate-700/40 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-600/20 border border-blue-500/30 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 10.741-2.342M12 13.489V21"/>
                        </svg>
                    </div>
                    <h3 class="text-white font-semibold">Informasi Sekolah</h3>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2" for="school_name">
                            Nama Sekolah <span class="text-red-400">*</span>
                        </label>
                        <input type="text" id="school_name" name="school_name"
                               value="{{ old('school_name', $settings?->school_name) }}"
                               placeholder="Contoh: SDN 30 Salayo"
                               class="w-full bg-slate-900/60 border border-slate-600/50 text-white placeholder-slate-500 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500/60 focus:ring-1 focus:ring-blue-500/40 transition-all @error('school_name') border-red-500/50 @enderror">
                        @error('school_name')
                        <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2" for="school_year">
                            Tahun Ajaran <span class="text-red-400">*</span>
                        </label>
                        <input type="text" id="school_year" name="school_year"
                               value="{{ old('school_year', $settings?->school_year) }}"
                               placeholder="Contoh: 2025/2026"
                               class="w-full bg-slate-900/60 border border-slate-600/50 text-white placeholder-slate-500 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500/60 focus:ring-1 focus:ring-blue-500/40 transition-all @error('school_year') border-red-500/50 @enderror">
                        @error('school_year')
                        <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2" for="copyright">
                            Teks Copyright
                        </label>
                        <input type="text" id="copyright" name="copyright"
                               value="{{ old('copyright', $settings?->copyright) }}"
                               placeholder="Contoh: © 2026 SDN 30 Salayo. All rights reserved."
                               class="w-full bg-slate-900/60 border border-slate-600/50 text-white placeholder-slate-500 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500/60 focus:ring-1 focus:ring-blue-500/40 transition-all">
                    </div>
                </div>
            </div>

            {{-- 2. Geofencing --}}
            <div class="bg-slate-800/60 border border-slate-700/50 rounded-2xl overflow-hidden backdrop-blur">
                <div class="px-6 py-4 border-b border-slate-700/40 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-600/20 border border-emerald-500/30 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                            </svg>
                        </div>
                        <h3 class="text-white font-semibold">Geofencing GPS</h3>
                    </div>
                    {{-- Toggle Geofencing --}}
                    <label class="relative inline-flex items-center cursor-pointer" title="Aktifkan/nonaktifkan validasi geofencing">
                        <input type="hidden" name="geofencing_enabled" value="0">
                        <input type="checkbox" id="geofencing_enabled" name="geofencing_enabled" value="1" class="sr-only peer"
                               {{ $settings?->geofencing_enabled ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        <span class="ms-2 text-xs text-slate-400 font-medium">Aktif</span>
                    </label>
                </div>
                <div class="p-6 space-y-5">
                    <div class="p-3.5 rounded-xl bg-blue-500/5 border border-blue-500/15">
                        <p class="text-xs text-blue-300/80">
                            💡 Geofencing memvalidasi bahwa scan QR dilakukan dalam radius tertentu dari koordinat sekolah.
                            Matikan jika sekolah tidak ingin membatasi lokasi absensi.
                        </p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2" for="school_latitude">
                                Latitude Sekolah <span class="text-red-400">*</span>
                            </label>
                            <input type="number" id="school_latitude" name="school_latitude" step="0.00000001"
                                   value="{{ old('school_latitude', $settings?->school_latitude) }}"
                                   placeholder="-0.9481234"
                                   class="w-full bg-slate-900/60 border border-slate-600/50 text-white placeholder-slate-500 rounded-xl px-4 py-3 text-sm font-mono focus:outline-none focus:border-emerald-500/60 focus:ring-1 focus:ring-emerald-500/40 transition-all @error('school_latitude') border-red-500/50 @enderror">
                            @error('school_latitude')
                            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2" for="school_longitude">
                                Longitude Sekolah <span class="text-red-400">*</span>
                            </label>
                            <input type="number" id="school_longitude" name="school_longitude" step="0.00000001"
                                   value="{{ old('school_longitude', $settings?->school_longitude) }}"
                                   placeholder="100.3612345"
                                   class="w-full bg-slate-900/60 border border-slate-600/50 text-white placeholder-slate-500 rounded-xl px-4 py-3 text-sm font-mono focus:outline-none focus:border-emerald-500/60 focus:ring-1 focus:ring-emerald-500/40 transition-all @error('school_longitude') border-red-500/50 @enderror">
                            @error('school_longitude')
                            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2" for="geofence_radius_meters">
                            Radius Geofencing (meter) <span class="text-red-400">*</span>
                        </label>
                        <div class="flex items-center gap-4">
                            <input type="range" id="radiusSlider" min="50" max="2000" step="50"
                                   value="{{ old('geofence_radius_meters', $settings?->geofence_radius_meters ?? 500) }}"
                                   oninput="document.getElementById('geofence_radius_meters').value=this.value; document.getElementById('radiusDisplay').textContent=this.value+'m'"
                                   class="flex-1 accent-emerald-500">
                            <span id="radiusDisplay" class="text-emerald-400 font-mono font-bold w-16 text-right text-sm">
                                {{ $settings?->geofence_radius_meters ?? 500 }}m
                            </span>
                        </div>
                        <input type="hidden" id="geofence_radius_meters" name="geofence_radius_meters"
                               value="{{ old('geofence_radius_meters', $settings?->geofence_radius_meters ?? 500) }}"
                               oninput="document.getElementById('radiusSlider').value=this.value; document.getElementById('radiusDisplay').textContent=this.value+'m'">
                        @error('geofence_radius_meters')
                        <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Ambil dari GPS --}}
                    <button type="button" onclick="getGPS()"
                            class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600/15 border border-emerald-500/25 text-emerald-400 hover:bg-emerald-600/25 transition-colors text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                        </svg>
                        <span id="gpsBtn">📍 Ambil Koordinat Saat Ini</span>
                    </button>
                </div>
            </div>

            {{-- 3. WhatsApp Notifikasi --}}
            <div class="bg-slate-800/60 border border-slate-700/50 rounded-2xl overflow-hidden backdrop-blur">
                <div class="px-6 py-4 border-b border-slate-700/40 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-green-600/20 border border-green-500/30 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/>
                            </svg>
                        </div>
                        <h3 class="text-white font-semibold">Notifikasi WhatsApp</h3>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="whatsapp_enabled" value="0">
                        <input type="checkbox" id="whatsapp_enabled" name="whatsapp_enabled" value="1" class="sr-only peer"
                               {{ $settings?->whatsapp_enabled ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        <span class="ms-2 text-xs text-slate-400 font-medium">Aktif</span>
                    </label>
                </div>
                <div class="p-6 space-y-5">
                    <div class="p-3.5 rounded-xl bg-green-500/5 border border-green-500/15">
                        <p class="text-xs text-green-300/80">
                            💬 Notifikasi WhatsApp menggunakan <strong>Fonnte API</strong>.
                            Token dapat diperoleh di <a href="https://fonnte.com" target="_blank" class="text-green-400 hover:underline">fonnte.com</a>.
                            Pastikan nomor HP orang tua sudah diisi di data siswa.
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2" for="whatsapp_token">
                            Fonnte API Token
                        </label>
                        <input type="password" id="whatsapp_token" name="whatsapp_token"
                               value="{{ old('whatsapp_token', $settings?->whatsapp_token) }}"
                               placeholder="Token dari dashboard Fonnte..."
                               autocomplete="new-password"
                               class="w-full bg-slate-900/60 border border-slate-600/50 text-white placeholder-slate-500 rounded-xl px-4 py-3 text-sm font-mono focus:outline-none focus:border-green-500/60 focus:ring-1 focus:ring-green-500/40 transition-all">
                        <p class="mt-1.5 text-xs text-slate-500">Biarkan kosong jika tidak ingin mengubah token yang tersimpan.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── Right Column: Save + Info ─── --}}
        <div class="space-y-5">

            {{-- Save Card --}}
            <div class="bg-slate-800/60 border border-slate-700/50 rounded-2xl p-5 backdrop-blur sticky top-24">
                <h4 class="text-white font-semibold mb-1">Simpan Perubahan</h4>
                <p class="text-xs text-slate-400 mb-5">Pengaturan ini berlaku untuk seluruh sistem absensi.</p>

                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm transition-all duration-200 shadow-lg shadow-blue-600/20 active:scale-95">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                    Simpan Pengaturan
                </button>

                @if(session('success'))
                <div class="mt-4 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                    </svg>
                    {{ session('success') }}
                </div>
                @endif
            </div>

            {{-- Current Settings Info --}}
            <div class="bg-slate-800/60 border border-slate-700/50 rounded-2xl p-5 backdrop-blur">
                <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Pengaturan Saat Ini</h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-400">Geofencing</span>
                        <span class="text-xs font-medium {{ $settings?->geofencing_enabled ? 'text-emerald-400' : 'text-slate-500' }}">
                            {{ $settings?->geofencing_enabled ? '✅ Aktif' : '⭕ Nonaktif' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-400">Radius</span>
                        <span class="text-xs font-mono text-white">{{ $settings?->geofence_radius_meters ?? '—' }} m</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-400">WhatsApp</span>
                        <span class="text-xs font-medium {{ $settings?->whatsapp_enabled ? 'text-green-400' : 'text-slate-500' }}">
                            {{ $settings?->whatsapp_enabled ? '✅ Aktif' : '⭕ Nonaktif' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-400">Tahun Ajaran</span>
                        <span class="text-xs text-white">{{ $settings?->school_year ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-400">Terakhir Update</span>
                        <span class="text-xs text-slate-400">
                            {{ $settings?->updated_at?->translatedFormat('d M Y H:i') ?? '—' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function getGPS() {
    const btn = document.getElementById('gpsBtn');
    if (!navigator.geolocation) {
        alert('Browser Anda tidak mendukung Geolocation.');
        return;
    }
    btn.textContent = '⏳ Mengambil koordinat...';
    navigator.geolocation.getCurrentPosition(
        (pos) => {
            document.getElementById('school_latitude').value  = pos.coords.latitude.toFixed(8);
            document.getElementById('school_longitude').value = pos.coords.longitude.toFixed(8);
            btn.textContent = '✅ Koordinat berhasil diambil!';
            setTimeout(() => btn.textContent = '📍 Ambil Koordinat Saat Ini', 3000);
        },
        () => {
            btn.textContent = '❌ Gagal ambil koordinat';
            setTimeout(() => btn.textContent = '📍 Ambil Koordinat Saat Ini', 3000);
        }
    );
}
</script>
@endpush
