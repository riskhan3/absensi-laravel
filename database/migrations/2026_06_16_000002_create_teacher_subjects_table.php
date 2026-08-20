<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Relasi guru ke mata pelajaran & kelas yang diajar
        Schema::create('teacher_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('classroom_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->unique(['teacher_id', 'subject_id', 'classroom_id'], 'teacher_subject_class_unique');
        });
    }

    public function down(): void { Schema::dropIfExists('teacher_subjects'); }
};
