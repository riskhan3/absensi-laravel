<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambahkan unique constraint pada tabel attendance_sessions
 * untuk mencegah race condition saat double-submit form absensi.
 *
 * Satu guru hanya boleh membuat satu sesi per kelas per hari.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_sessions', function (Blueprint $table) {
            // Pastikan tidak ada duplikat sebelum menambahkan constraint
            // Unique: 1 guru - 1 kelas - 1 hari = 1 sesi
            $table->unique(
                ['classroom_id', 'teacher_id', 'date'],
                'attendance_sessions_unique_per_teacher_per_class_per_day'
            );
        });
    }

    public function down(): void
    {
        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->dropUnique('attendance_sessions_unique_per_teacher_per_class_per_day');
        });
    }
};
