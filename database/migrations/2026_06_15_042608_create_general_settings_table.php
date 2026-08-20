<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->string('school_name')->default('SD Negeri 30 Selayo');
            $table->string('school_year')->default('2025/2026');
            $table->string('logo')->nullable();
            $table->string('copyright')->default('© 2025 SD Negeri 30 Selayo');
            // Geofencing
            $table->decimal('school_latitude',  10, 8)->nullable()->comment('Lat GPS pusat sekolah');
            $table->decimal('school_longitude', 11, 8)->nullable()->comment('Lon GPS pusat sekolah');
            $table->unsignedInteger('geofence_radius_meters')->default(500);
            $table->boolean('geofencing_enabled')->default(true);
            // WhatsApp
            $table->string('whatsapp_token')->nullable();
            $table->boolean('whatsapp_enabled')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('general_settings'); }
};
