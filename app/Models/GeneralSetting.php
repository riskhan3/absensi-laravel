<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class GeneralSetting extends Model
{
    protected $fillable = [
        'school_name', 'school_year', 'logo', 'copyright',
        'school_latitude', 'school_longitude', 'geofence_radius_meters', 'geofencing_enabled',
        'whatsapp_token', 'whatsapp_enabled',
    ];

    protected $casts = [
        'school_latitude'     => 'float',
        'school_longitude'    => 'float',
        'geofencing_enabled'  => 'boolean',
        'whatsapp_enabled'    => 'boolean',
    ];

    /**
     * Selalu ambil row pertama (singleton pattern) dengan caching 30 menit.
     * Cache dibersihkan otomatis saat data diupdate.
     */
    public static function instance(): self
    {
        return Cache::remember('general_settings', now()->addMinutes(30), function () {
            return static::firstOrCreate([], [
                'school_name'             => 'Smart Absensi SDN 30 Salayo',
                'school_year'             => '2025/2026',
                'school_latitude'         => -0.8175879,
                'school_longitude'        => 100.6331262,
                'geofence_radius_meters'  => 500,
                'geofencing_enabled'      => true,
                'whatsapp_enabled'        => false,
            ]);
        });
    }

    /** Hapus cache saat data diubah */
    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('general_settings');
        });
    }
}
