<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Satu sesi absen = 1 kelas + 1 hari + dibuat oleh 1 guru
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['classroom_id', 'teacher_id', 'date'], 'attn_session_unique');
        });

        // Pivot: satu sesi bisa punya banyak mapel
        Schema::create('attendance_session_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->unique(['attendance_session_id', 'subject_id'], 'ass_session_subject_unique');
        });

        // Detail absen per siswa per sesi
        Schema::create('session_student_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attendance_status_id')->constrained()->cascadeOnDelete();
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->unique(['attendance_session_id', 'student_id'], 'ssa_session_student_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_student_attendances');
        Schema::dropIfExists('attendance_session_subjects');
        Schema::dropIfExists('attendance_sessions');
    }
};
