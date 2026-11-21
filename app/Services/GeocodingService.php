<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('cargotrace.geocoding_api_url', 'https://nominatim.openstreetmap.org');
    }

    /**
     * Forward geocode address or city to coordinates.
     * Caches result for 24 hours to be resilient and respect rate limits.
     */
    public function geocode(string $query): ?array
    {
        $cacheKey = 'geocode_' . md5(strtolower(trim($query)));

        return Cache::remember($cacheKey, 86400, function () use ($query) {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'CargoTrace/1.0 (Logistics Platform)',
                ])
                ->timeout(5)
                ->retry(2, 200)
                ->get("{$this->baseUrl}/search", [
                    'q' => $query,
                    'format' => 'json',
                    'limit' => 1,
                ]);

                if ($response->successful() && !empty($response->json())) {
                    $item = $response->json()[0];
                    return [
                        'latitude' => (float) $item['lat'],
                        'longitude' => (float) $item['lon'],
                        'display_name' => $item['display_name'] ?? $query,
                    ];
                }
            } catch (Exception $e) {
                Log::warning("Geocoding failed for query [{$query}]: " . $e->getMessage());
            }

            // Fallback for well-known hub locations if network fails
            return $this->getStaticFallbackCoordinates($query);
        });
    }

    /**
     * Reverse geocode coordinates to readable location name.
     */
    public function reverseGeocode(float $latitude, float $longitude): ?string
    {
        $cacheKey = "revgeo_{$latitude}_{$longitude}";

        return Cache::remember($cacheKey, 86400, function () use ($latitude, $longitude) {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'CargoTrace/1.0 (Logistics Platform)',
                ])
                ->timeout(5)
                ->get("{$this->baseUrl}/reverse", [
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'format' => 'json',
                ]);

                if ($response->successful()) {
                    return $response->json('display_name') ?? "Lat: {$latitude}, Lon: {$longitude}";
                }
            } catch (Exception $e) {
                Log::warning("Reverse geocoding failed for [{$latitude}, {$longitude}]: " . $e->getMessage());
            }

            return "Lat: {$latitude}, Lon: {$longitude}";
        });
    }

    /**
     * Resilient offline fallback dictionary for major maritime and logistics hubs.
     */
    protected function getStaticFallbackCoordinates(string $query): ?array
    {
        $normalized = strtolower($query);
        $known = [
            'jakarta' => ['latitude' => -6.2088, 'longitude' => 106.8456, 'display_name' => 'Tanjung Priok, Jakarta, Indonesia'],
            'singapore' => ['latitude' => 1.3521, 'longitude' => 103.8198, 'display_name' => 'Port of Singapore, Singapore'],
            'rotterdam' => ['latitude' => 51.9244, 'longitude' => 4.4777, 'display_name' => 'Port of Rotterdam, Netherlands'],
            'shanghai' => ['latitude' => 31.2304, 'longitude' => 121.4737, 'display_name' => 'Port of Shanghai, China'],
            'los angeles' => ['latitude' => 33.7432, 'longitude' => -118.2673, 'display_name' => 'Port of Los Angeles, USA'],
            'dubai' => ['latitude' => 25.2048, 'longitude' => 55.2708, 'display_name' => 'Jebel Ali Port, Dubai, UAE'],
            'surabaya' => ['latitude' => -7.2575, 'longitude' => 112.7521, 'display_name' => 'Tanjung Perak, Surabaya, Indonesia'],
        ];

        foreach ($known as $city => $coords) {
            if (str_contains($normalized, $city)) {
                return $coords;
            }
        }

        return null;
    }
}
