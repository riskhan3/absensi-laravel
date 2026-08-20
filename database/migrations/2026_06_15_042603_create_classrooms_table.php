<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('grade', 10);           // Contoh: I, II, III, IV, V, VI
            $table->foreignId('major_id')->nullable()->constrained('majors')->nullOnDelete();
            $table->string('label', 5);            // Contoh: A, B
            $table->foreignId('homeroom_teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void { Schema::dropIfExists('classrooms'); }
};
