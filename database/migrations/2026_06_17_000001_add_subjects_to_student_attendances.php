<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tabel pivot: satu record absensi siswa bisa punya banyak mapel
        Schema::create('student_attendance_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_attendance_id')->constrained('student_attendances')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['student_attendance_id', 'subject_id'], 'sas_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_attendance_subjects');
    }
};
