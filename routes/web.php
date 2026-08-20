<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Scanner\AttendanceController as ScanController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboard;
use App\Http\Controllers\Teacher\AttendanceSessionController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\AttendanceReportController;
use App\Http\Controllers\Admin\ManualAttendanceController;
use App\Http\Controllers\Admin\GeneralSettingController;
use Illuminate\Support\Facades\Route;

// ── AUTENTIKASI ───────────────────────────────────────────────────────────────
Route::get('/login',  [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ── SCANNER (Wajib Login sebagai Guru / Admin) ────────────────────────────────
// throttle:30,1 = maks 30 req/menit per IP (mencegah brute force scan_code)
Route::middleware(['auth', 'role:wali_kelas,guru_mapel,superadmin,admin'])
    ->prefix('scan')->name('scan.')->group(function () {
    // Halaman pemilihan scanner
    Route::get('/',            [ScanController::class, 'index'])->name('index');
    // Scanner 1: Siswa (pilih mapel)
    Route::get('/siswa',       [ScanController::class, 'indexSiswa'])->name('siswa');
    // Scanner 2: Guru & Staf (masuk/pulang)
    Route::get('/guru-staf',   [ScanController::class, 'indexGuruStaf'])->name('guru-staf');
    // API endpoints (shared) — throttle lebih ketat
    Route::post('/check-in',   [ScanController::class, 'checkIn'])->name('check-in')->middleware('throttle:30,1');
    Route::post('/check-out',  [ScanController::class, 'checkOut'])->name('check-out')->middleware('throttle:30,1');
    // Absen Manual (guru bisa input manual tanpa QR)
    Route::get('/absen-manual',        [ManualAttendanceController::class, 'index'])->name('absen-manual.index');
    Route::post('/absen-manual/store', [ManualAttendanceController::class, 'store'])->name('absen-manual.store');
});

// ── ADMIN AREA ────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:superadmin,admin,tu,kepala_sekolah'])
    ->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard',     [AdminDashboard::class, 'index'])->name('dashboard');
    Route::post('/filter-class', [AdminDashboard::class, 'filterByClass'])->name('filter-class');

    // CRUD Siswa
    Route::resource('students', StudentController::class);
    Route::get('students/{student}/qr', [StudentController::class, 'generateQr'])->name('students.qr');

    // CRUD Staf & Guru
    Route::resource('staff', \App\Http\Controllers\Admin\StaffController::class);
    Route::get('staff/{staff}/qr',          [\App\Http\Controllers\Admin\StaffController::class, 'generateQr'])->name('staff.qr');

    // Guru (nested under staff controller)
    Route::get('/teachers/create',           [\App\Http\Controllers\Admin\StaffController::class, 'createTeacher'])->name('teachers.create');
    Route::post('/teachers',                 [\App\Http\Controllers\Admin\StaffController::class, 'storeTeacher'])->name('teachers.store');
    Route::get('/teachers/{teacher}/edit',   [\App\Http\Controllers\Admin\StaffController::class, 'editTeacher'])->name('teachers.edit');
    Route::put('/teachers/{teacher}',        [\App\Http\Controllers\Admin\StaffController::class, 'updateTeacher'])->name('teachers.update');
    Route::delete('/teachers/{teacher}',     [\App\Http\Controllers\Admin\StaffController::class, 'destroyTeacher'])->name('teachers.destroy');
    Route::get('/teachers/{teacher}/qr',     [\App\Http\Controllers\Admin\StaffController::class, 'generateQrTeacher'])->name('teachers.qr');

    // Mata Pelajaran
    Route::resource('subjects', \App\Http\Controllers\Admin\SubjectController::class)->only(['index', 'store', 'update', 'destroy']);

    // ── Laporan Absensi ──────────────────────────────────────────────────────
    Route::get('/reports',          [AttendanceReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/download', [AttendanceReportController::class, 'download'])->name('reports.download');
    Route::get('/reports/print',    [AttendanceReportController::class, 'print'])->name('reports.print');

    // ── Absen Manual Siswa ───────────────────────────────────────────────────
    Route::get('/manual-attendance',        [ManualAttendanceController::class, 'index'])->name('manual-attendance.index');
    Route::post('/manual-attendance/store', [ManualAttendanceController::class, 'store'])->name('manual-attendance.store');

    // ── Pengaturan Sistem ────────────────────────────────────────────────────
    Route::middleware('role:superadmin,admin')->group(function () {
        Route::get('/settings',  [GeneralSettingController::class, 'index'])->name('settings.index');
        Route::put('/settings',  [GeneralSettingController::class, 'update'])->name('settings.update');
    });
});

// ── TEACHER AREA (wali_kelas & guru_mapel) ────────────────────────────────────
Route::middleware(['auth', 'role:wali_kelas,guru_mapel,superadmin,admin'])
    ->prefix('teacher')->name('teacher.')->group(function () {

    Route::get('/dashboard', [TeacherDashboard::class, 'index'])->name('dashboard');

    // Sesi Absensi (core feature)
    Route::resource('sessions', AttendanceSessionController::class);

    // Absen Manual Siswa (tanpa QR — akses guru via sidebar)
    Route::get('/manual-attendance',        [ManualAttendanceController::class, 'index'])->name('manual-attendance.index');
    Route::post('/manual-attendance/store', [ManualAttendanceController::class, 'store'])->name('manual-attendance.store');

    // Scanner Absensi Kelas (QR/RFID di kelas) — kelas auto-detect dari data siswa
    Route::get('/classroom-scan',             [\App\Http\Controllers\Teacher\ClassroomScanController::class, 'index'])->name('classroom-scan.index');
    Route::post('/classroom-scan/scan',       [\App\Http\Controllers\Teacher\ClassroomScanController::class, 'scan'])->name('classroom-scan.scan');
    Route::post('/classroom-scan/review',     [\App\Http\Controllers\Teacher\ClassroomScanController::class, 'review'])->name('classroom-scan.review');
    Route::post('/classroom-scan/finalize',   [\App\Http\Controllers\Teacher\ClassroomScanController::class, 'finalize'])->name('classroom-scan.finalize');
});

// ── ROOT REDIRECT ─────────────────────────────────────────────────────────────
Route::get('/', function () {
    if (auth()->check()) {
        return match(auth()->user()->role) {
            'superadmin', 'admin', 'tu' => redirect()->route('admin.dashboard'),
            'kepala_sekolah'            => redirect()->route('admin.dashboard'),
            'wali_kelas', 'guru_mapel'  => redirect()->route('scan.index'),
            default                     => redirect()->route('login'),
        };
    }
    return redirect()->route('login');
});
