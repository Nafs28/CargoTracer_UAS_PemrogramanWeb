<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('cargotrace.weather_api_url', 'https://api.open-meteo.com/v1');
    }

    /**
     * Fetch real-time maritime/surface weather for coordinates via Open-Meteo API.
     * Caches response for 30 minutes to reduce outbound traffic and ensure resiliency.
     */
    public function getWeather(float $latitude, float $longitude): array
    {
        $cacheKey = "weather_" . round($latitude, 2) . "_" . round($longitude, 2);

        return Cache::remember($cacheKey, 1800, function () use ($latitude, $longitude) {
            try {
                $response = Http::timeout(5)
                    ->retry(2, 200)
                    ->get("{$this->baseUrl}/forecast", [
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'current' => 'temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code',
                        'timezone' => 'auto',
                    ]);

                if ($response->successful()) {
                    $current = $response->json('current') ?? [];
                    return [
                        'success' => true,
                        'temperature' => $current['temperature_2m'] ?? null,
                        'humidity' => $current['relative_humidity_2m'] ?? null,
                        'wind_speed' => $current['wind_speed_10m'] ?? null,
                        'condition' => $this->interpretWeatherCode($current['weather_code'] ?? 0),
                        'weather_code' => $current['weather_code'] ?? 0,
                    ];
                }
            } catch (Exception $e) {
                Log::warning("Weather API failed for [{$latitude}, {$longitude}]: " . $e->getMessage());
            }

            // Resilient fallback when external API is unreachable or rate limited
            return [
                'success' => false,
                'temperature' => 28.0,
                'humidity' => 75,
                'wind_speed' => 12.5,
                'condition' => 'Clear Skies (Fallback)',
                'weather_code' => 0,
            ];
        });
    }

    /**
     * Convert WMO weather code to readable string.
     */
    protected function interpretWeatherCode(int $code): string
    {
        return match ($code) {
            0 => 'Clear sky',
            1, 2, 3 => 'Partly cloudy',
            45, 48 => 'Foggy',
            51, 53, 55 => 'Drizzle',
            61, 63, 65 => 'Rain',
            71, 73, 75 => 'Snow',
            80, 81, 82 => 'Rain showers',
            95, 96, 99 => 'Thunderstorm',
            default => 'Fair',
        };
    }
}
