<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Location\StoreLocationRequest;
use App\Models\Location;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    protected AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function index(Request $request): JsonResponse
    {
        $locations = Location::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $locations,
        ], 200);
    }

    public function store(StoreLocationRequest $request): JsonResponse
    {
        $location = Location::create($request->validated());
        $this->auditService->log($request->user(), 'CREATE_LOCATION', $location, null, $location->toArray());

        return response()->json([
            'success' => true,
            'message' => 'Location created successfully.',
            'data' => $location,
        ], 201);
    }

    public function show(Location $location): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $location,
        ], 200);
    }

    public function update(StoreLocationRequest $request, Location $location): JsonResponse
    {
        $old = $location->toArray();
        $location->update($request->validated());
        $this->auditService->log($request->user(), 'UPDATE_LOCATION', $location, $old, $location->toArray());

        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully.',
            'data' => $location,
        ], 200);
    }

    public function destroy(Request $request, Location $location): JsonResponse
    {
        if ($location->shipmentsAsOrigin()->exists() || $location->shipmentsAsDestination()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete location that is referenced in existing shipments.',
            ], 422);
        }

        $old = $location->toArray();
        $location->delete();
        $this->auditService->log($request->user(), 'DELETE_LOCATION', $location, $old, null);

        return response()->json([
            'success' => true,
            'message' => 'Location deleted successfully.',
        ], 200);
    }
}
