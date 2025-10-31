<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class LokasiKerja extends Model
{
    protected $table = 'lokasi_kerja';
    protected $primaryKey = 'id_lokasi';

    protected $fillable = [
        'lokasi_kerja',
        'latitude',
        'longitude',
        'radius',
    ];

    /**
     * Check if a given latitude and longitude is within the radius of this location.
     *
     * @param float $lat
     * @param float $lng
     * @return bool
     */
    public function isWithinRadius($lat, $lng)
    {
        // Validate input coordinates
        if ($lat === null || $lng === null) {
            return false;
        }
        
        // Check if location has coordinates and radius set
        if (!$this->latitude || !$this->longitude || !$this->radius) {
            return false; // If location not set, consider not within
        }

        try {
            $distance = $this->calculateDistance($this->latitude, $this->longitude, $lat, $lng);
            return $distance <= $this->radius;
        } catch (\Exception $e) {
            Log::error('Error calculating distance: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get distance from this location to given coordinates
     *
     * @param float $lat
     * @param float $lng
     * @return float Distance in meters
     */
    public function getDistanceFrom($lat, $lng)
    {
        if ($lat === null || $lng === null) {
            return 0;
        }
        
        if (!$this->latitude || !$this->longitude) {
            return 0;
        }

        try {
            return $this->calculateDistance($this->latitude, $this->longitude, $lat, $lng);
        } catch (\Exception $e) {
            Log::error('Error calculating distance: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Calculate the distance between two points using Haversine formula.
     *
     * @param float $lat1
     * @param float $lng1
     * @param float $lat2
     * @param float $lng2
     * @return float Distance in meters
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000; // Earth radius in meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
