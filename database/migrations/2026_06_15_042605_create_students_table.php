<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nis', 16)->unique();
            $table->string('name');
            $table->foreignId('classroom_id')->constrained('classrooms');
            $table->enum('gender', ['Laki-laki', 'Perempuan']);
            $table->string('phone', 32)->nullable()->comment('No HP orang tua/wali');
            $table->string('unique_code', 64)->unique();
            $table->string('rfid_code', 100)->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void { Schema::dropIfExists('students'); }
};
