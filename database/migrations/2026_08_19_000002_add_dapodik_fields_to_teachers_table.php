<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            // Kolom tambahan dari Dapodik
            $table->string('nip', 30)->nullable()->unique()->after('nuptk')->comment('NIP ASN/PPPK');
            $table->string('nik', 16)->nullable()->after('nip')->comment('NIK guru');
            $table->date('birth_date')->nullable()->after('gender')->comment('Tanggal lahir guru');
            $table->string('employment_status')->nullable()->after('birth_date')->comment('PNS, PPPK, PPPK Paruh Waktu, Tenaga Honor Sekolah');
            $table->string('position')->nullable()->after('employment_status')->comment('Jabatan: Guru, Kepala Sekolah, dll.');
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn(['nip', 'nik', 'birth_date', 'employment_status', 'position']);
        });
    }
};
