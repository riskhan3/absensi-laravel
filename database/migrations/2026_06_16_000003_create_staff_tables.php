<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 30)->unique()->nullable();
            $table->string('name');
            $table->string('position')->nullable();  // Jabatan: Staf TU, Penjaga Sekolah, dll
            $table->enum('gender', ['Laki-laki', 'Perempuan'])->nullable();
            $table->string('phone', 32)->nullable();
            $table->string('unique_code', 100)->unique();  // untuk QR code
            $table->string('rfid_code', 100)->unique()->nullable();
            $table->timestamps();
        });

        Schema::create('staff_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->unique(['staff_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_attendances');
        Schema::dropIfExists('staff');
    }
};
