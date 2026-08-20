<?php

namespace App\Services;

use App\Exceptions\GeofencingException;
use App\Models\GeneralSetting;

class GeofencingService
{
    private GeneralSetting $settings;

    public function __construct()
    {
        $this->settings = GeneralSetting::instance();
    }

    public function validate(float $lat, float $lon): void
    {
        if (!$this->settings->geofencing_enabled) return;

        if ($lat === 0.0 && $lon === 0.0) {
            throw new GeofencingException('Koordinat GPS tidak valid. Aktifkan GPS perangkat Anda.');
        }

        $distance = $this->haversineDistance($lat, $lon, $this->settings->school_latitude, $this->settings->school_longitude);

        if ($distance > $this->settings->geofence_radius_meters) {
            throw new GeofencingException(
                sprintf('Di luar radius sekolah. Jarak Anda: %d meter. Batas: %d meter.', round($distance), $this->settings->geofence_radius_meters)
            );
        }
    }

    public function calculateDistance(float $lat, float $lon): float
    {
        return $this->haversineDistance($lat, $lon, $this->settings->school_latitude, $this->settings->school_longitude);
    }

    private function haversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R  = 6_371_000;
        $φ1 = deg2rad($lat1); $φ2 = deg2rad($lat2);
        $Δφ = deg2rad($lat2 - $lat1); $Δλ = deg2rad($lon2 - $lon1);
        $a  = sin($Δφ/2)**2 + cos($φ1) * cos($φ2) * sin($Δλ/2)**2;
        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
