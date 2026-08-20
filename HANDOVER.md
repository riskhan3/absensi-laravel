# 📋 HANDOVER DOCUMENT — Sistem Absensi Laravel
## SD Negeri 30 Selayo

---

## 🎯 Deskripsi Proyek

Sistem absensi sekolah berbasis **Laravel 12 + Tailwind CSS** (via CDN) dengan fitur:
- ✅ Absensi QR Code & RFID
- ✅ Validasi Radius Geofencing (Haversine Formula, 500m)
- ✅ Notifikasi WhatsApp via Fonnte API (Queue)
- ✅ Dashboard Admin (ApexCharts)
- ✅ Dashboard Wali Kelas (hanya data kelasnya)
- ✅ Multi-role: superadmin, admin, wali_kelas, scanner

---

## 📁 Lokasi Proyek

```
c:\Users\RISKHAN\Downloads\Bahan2 TA\absensi-laravel\
```

---

## 🔧 Tech Stack

| Komponen | Detail |
|---|---|
| Framework | Laravel 12.x |
| PHP | 8.2.12 (XAMPP) |
| Database | MariaDB 10.4 (XAMPP MySQL) |
| Frontend | Tailwind CSS CDN + Alpine.js CDN + ApexCharts CDN |
| Queue | Database driver |
| Session | File driver |
| WhatsApp | Fonnte API |

---

## 🗄️ Database

**Nama DB:** `db_absensi_laravel`
**User:** `root` / **Password:** (kosong)
**Port:** `3306`

### Tabel yang ada (11 tabel):
- `users` — role: superadmin, admin, wali_kelas, scanner
- `teachers` — data guru + NUPTK + RFID code
- `classrooms` — data kelas + wali kelas
- `students` — data siswa + NIS + RFID code
- `majors` — jurusan (default: Umum)
- `student_attendances` — presensi siswa (+ koordinat GPS)
- `teacher_attendances` — presensi guru (+ koordinat GPS)
- `attendance_statuses` — Hadir, Sakit, Izin, Tanpa Keterangan
- `general_settings` — konfigurasi sekolah + geofencing
- `jobs` — queue jobs
- `sessions`, `cache`, dll.

### Jalankan ulang DB (jika kosong):
```bash
php artisan migrate:fresh --seed --force
```

---

## 🔑 Akun Login Default

| Role | Email | Password |
|---|---|---|
| Super Admin | superadmin@sekolah.com | admin123 |
| Admin | admin@sekolah.com | admin123 |
| Wali Kelas | walikelas@sekolah.com | wali123 |
| Scanner | scanner@sekolah.com | scanner123 |

---

## 🏫 Konfigurasi Sekolah (di .env dan GeneralSetting)

```
school_name       = SD Negeri 30 Selayo
school_latitude   = -0.8175879
school_longitude  = 100.6331262
geofence_radius   = 500 meter
```

---

## ⚠️ MASALAH DIKETAHUI — MySQL Tidak Stabil

**Gejala:** Error `SQLSTATE[HY000] [2002] No connection could be made` karena MySQL XAMPP mati.

**Penyebab:** MySQL XAMPP berjalan via proses manual (bukan Windows Service).

**Solusi 3 cara (pilih salah satu):**

### Cara 1 — XAMPP Control Panel (termudah)
1. Buka XAMPP Control Panel
2. Klik **Start** di baris MySQL
3. Baru jalankan `php artisan serve`

### Cara 2 — Script otomatis (double klik)
```
absensi-laravel\START_ABSENSI.bat
```

### Cara 3 — Install MySQL sebagai Windows Service (permanen)
> Jalankan CMD sebagai **Administrator**, lalu:
```cmd
C:\xampp\mysql\bin\mysqld.exe --install MySQL --defaults-file="C:\xampp\mysql\bin\my.ini"
net start MySQL
```
Setelah ini MySQL akan **otomatis aktif** setiap Windows boot.

---

## 🚀 Cara Menjalankan

```bash
# 1. Pastikan MySQL aktif (via XAMPP atau service)

# 2. Jalankan server Laravel
cd "c:\Users\RISKHAN\Downloads\Bahan2 TA\absensi-laravel"
php artisan serve --port=8001

# 3. Buka browser
http://127.0.0.1:8001/login
```

### URL Penting:
| Halaman | URL |
|---|---|
| Login | http://127.0.0.1:8001/login |
| Scanner | http://127.0.0.1:8001/scan |
| Admin Dashboard | http://127.0.0.1:8001/admin/dashboard |
| Wali Kelas | http://127.0.0.1:8001/teacher/dashboard |

---

## 📂 Struktur File Penting

```
absensi-laravel/
├── app/
│   ├── Exceptions/AttendanceExceptions.php   ← Custom exceptions
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/LoginController.php
│   │   │   ├── Admin/DashboardController.php
│   │   │   ├── Teacher/DashboardController.php
│   │   │   └── Scanner/AttendanceController.php
│   │   ├── Middleware/RoleMiddleware.php
│   │   └── Requests/ScanAttendanceRequest.php
│   ├── Jobs/SendWhatsAppNotificationJob.php  ← Queue WA
│   ├── Models/                               ← 9 Models Eloquent
│   └── Services/
│       ├── AttendanceService.php             ← Logika utama absensi
│       ├── GeofencingService.php             ← Haversine formula
│       └── WhatsAppService.php               ← Fonnte API
├── database/
│   ├── migrations/                           ← 11 file migrasi
│   └── seeders/DatabaseSeeder.php            ← Data demo lengkap
├── resources/views/
│   ├── auth/login.blade.php
│   ├── layouts/app.blade.php
│   ├── scanner/index.blade.php               ← Halaman scanner utama
│   ├── admin/dashboard.blade.php
│   └── teacher/dashboard.blade.php
├── routes/web.php
├── bootstrap/app.php                         ← RoleMiddleware didaftarkan
├── .env                                      ← Konfigurasi utama
└── START_ABSENSI.bat                         ← Script satu klik

```

---

## 📝 Fitur Belum Dibuat (Next Steps)

- [ ] CRUD Siswa (tambah/edit/hapus)
- [ ] CRUD Guru
- [ ] CRUD Kelas
- [ ] Laporan Absensi (export PDF/Excel)
- [ ] Halaman pengaturan umum (ubah koordinat, radius, WA token)
- [ ] Manual input absensi oleh admin
- [ ] QR Code generator untuk siswa/guru

---

## 🔗 Referensi Lama

Sistem ini adalah **rewrite** dari project lama berbasis CodeIgniter 4:
```
c:\Users\RISKHAN\Downloads\Bahan2 TA\bahan sourcode code igniter 4\
```

---

*Dokumen dibuat: 16 Juni 2026*
