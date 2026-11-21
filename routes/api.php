<?php

use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarrierController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\GeocodingController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\ShipmentController;
use App\Http\Controllers\Api\ShipmentExceptionController;
use App\Http\Controllers\Api\TrackingEventController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - CargoTrace Platform
|--------------------------------------------------------------------------
*/

// Public Authentication Endpoints
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Endpoints (Requires Laravel Sanctum Token)
Route::middleware('auth:sanctum')->group(function () {
    // Auth Session
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Operational Analytics Dashboard (Scoped by Role)
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index']);
        Route::get('/shipments-by-status', [DashboardController::class, 'shipmentsByStatus']);
        Route::get('/shipments-by-carrier', [DashboardController::class, 'shipmentsByCarrier']);
        Route::get('/delay-trend', [DashboardController::class, 'delayTrend']);
    });

    // Shipments Resource
    Route::get('/shipments', [ShipmentController::class, 'index']);
    Route::post('/shipments', [ShipmentController::class, 'store']);
    Route::get('/shipments/{shipment}', [ShipmentController::class, 'show']);
    Route::match(['put', 'patch'], '/shipments/{shipment}', [ShipmentController::class, 'update'])
        ->middleware('role:admin,operator');
    Route::delete('/shipments/{shipment}', [ShipmentController::class, 'destroy'])
        ->middleware('role:admin');
    Route::patch('/shipments/{shipment}/status', [ShipmentController::class, 'updateStatus'])
        ->middleware('role:admin,operator');
    Route::get('/shipments/{shipment}/map', [ShipmentController::class, 'map']);

    // Tracking Events (Append-Only History)
    Route::get('/shipments/{shipment}/tracking', [TrackingEventController::class, 'index']);
    Route::post('/shipments/{shipment}/tracking', [TrackingEventController::class, 'store'])
        ->middleware('role:admin,operator');

    // Exceptions Lifecycle Module
    Route::get('/shipments/{shipment}/exceptions', [ShipmentExceptionController::class, 'index']);
    Route::post('/shipments/{shipment}/exceptions', [ShipmentExceptionController::class, 'store'])
        ->middleware('role:admin,operator');
    Route::get('/exceptions', [ShipmentExceptionController::class, 'index'])
        ->middleware('role:admin,operator');
    Route::patch('/exceptions/{exception}', [ShipmentExceptionController::class, 'update'])
        ->middleware('role:admin,operator');

    // Carriers Master Data
    Route::get('/carriers', [CarrierController::class, 'index']);
    Route::post('/carriers', [CarrierController::class, 'store'])->middleware('role:admin');
    Route::get('/carriers/{carrier}', [CarrierController::class, 'show']);
    Route::match(['put', 'patch'], '/carriers/{carrier}', [CarrierController::class, 'update'])->middleware('role:admin');
    Route::delete('/carriers/{carrier}', [CarrierController::class, 'destroy'])->middleware('role:admin');

    // Hub & Port Locations Master Data
    Route::get('/locations', [LocationController::class, 'index']);
    Route::post('/locations', [LocationController::class, 'store'])->middleware('role:admin');
    Route::get('/locations/{location}', [LocationController::class, 'show']);
    Route::match(['put', 'patch'], '/locations/{location}', [LocationController::class, 'update'])->middleware('role:admin');
    Route::delete('/locations/{location}', [LocationController::class, 'destroy'])->middleware('role:admin');

    // Resilient GIS & Weather External API Integrations
    Route::get('/geocode', [GeocodingController::class, 'geocode']);
    Route::get('/reverse-geocode', [GeocodingController::class, 'reverseGeocode']);
    Route::get('/weather', [GeocodingController::class, 'weather']);

    // Audit Logs (Admin only)
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->middleware('role:admin');
});
