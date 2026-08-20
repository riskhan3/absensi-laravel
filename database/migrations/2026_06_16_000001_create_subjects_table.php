<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // e.g. "Matematika"
            $table->string('code', 20);       // e.g. "MTK"
            $table->enum('class_group', ['1-2', '3-6', 'all'])->default('all');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('subjects'); }
};
