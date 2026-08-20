<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Kolom tambahan dari Dapodik
            $table->string('nisn', 20)->nullable()->unique()->after('nis')->comment('NISN resmi dari Dapodik');
            $table->date('birth_date')->nullable()->after('gender')->comment('Tanggal lahir siswa');
            $table->string('mother_name')->nullable()->after('birth_date')->comment('Nama ibu kandung');
            $table->string('nik', 16)->nullable()->after('mother_name')->comment('NIK siswa');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['nisn', 'birth_date', 'mother_name', 'nik']);
        });
    }
};
