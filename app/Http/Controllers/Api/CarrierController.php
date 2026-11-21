<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Carrier\StoreCarrierRequest;
use App\Models\Carrier;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CarrierController extends Controller
{
    protected AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function index(Request $request): JsonResponse
    {
        $carriers = Carrier::withCount('shipments')->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $carriers,
        ], 200);
    }

    public function store(StoreCarrierRequest $request): JsonResponse
    {
        $carrier = Carrier::create($request->validated());
        $this->auditService->log($request->user(), 'CREATE_CARRIER', $carrier, null, $carrier->toArray());

        return response()->json([
            'success' => true,
            'message' => 'Carrier created successfully.',
            'data' => $carrier,
        ], 201);
    }

    public function show(Carrier $carrier): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $carrier->load('shipments'),
        ], 200);
    }

    public function update(StoreCarrierRequest $request, Carrier $carrier): JsonResponse
    {
        $old = $carrier->toArray();
        $carrier->update($request->validated());
        $this->auditService->log($request->user(), 'UPDATE_CARRIER', $carrier, $old, $carrier->toArray());

        return response()->json([
            'success' => true,
            'message' => 'Carrier updated successfully.',
            'data' => $carrier,
        ], 200);
    }

    public function destroy(Request $request, Carrier $carrier): JsonResponse
    {
        if ($carrier->shipments()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete carrier with active linked shipments.',
            ], 422);
        }

        $old = $carrier->toArray();
        $carrier->delete();
        $this->auditService->log($request->user(), 'DELETE_CARRIER', $carrier, $old, null);

        return response()->json([
            'success' => true,
            'message' => 'Carrier deleted successfully.',
        ], 200);
    }
}
