<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GeocodingService;
use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeocodingController extends Controller
{
    protected GeocodingService $geocodingService;
    protected WeatherService $weatherService;

    public function __construct(GeocodingService $geocodingService, WeatherService $weatherService)
    {
        $this->geocodingService = $geocodingService;
        $this->weatherService = $weatherService;
    }

    /**
     * Forward geocode query to coordinates.
     */
    public function geocode(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string']);
        $result = $this->geocodingService->geocode($request->q);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Location not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ], 200);
    }

    /**
     * Reverse geocode coordinates to location name.
     */
    public function reverseGeocode(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lon' => 'required|numeric|between:-180,180',
        ]);

        $result = $this->geocodingService->reverseGeocode((float) $request->lat, (float) $request->lon);

        return response()->json([
            'success' => true,
            'data' => ['address' => $result],
        ], 200);
    }

    /**
     * Get resilient weather data for coordinates.
     */
    public function weather(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lon' => 'required|numeric|between:-180,180',
        ]);

        $weather = $this->weatherService->getWeather((float) $request->lat, (float) $request->lon);

        return response()->json([
            'success' => true,
            'data' => $weather,
        ], 200);
    }
}
