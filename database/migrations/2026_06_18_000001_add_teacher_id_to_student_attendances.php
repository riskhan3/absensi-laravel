<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_attendances', function (Blueprint $table) {
            // Guru yang mengambil absen (nullable, tidak wajib)
            $table->foreignId('teacher_id')->nullable()->after('classroom_id')
                  ->constrained('teachers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('student_attendances', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Teacher::class);
            $table->dropColumn('teacher_id');
        });
    }
};
