<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrackingEvent\StoreTrackingEventRequest;
use App\Models\Shipment;
use App\Services\TrackingService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrackingEventController extends Controller
{
    protected TrackingService $trackingService;

    public function __construct(TrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    /**
     * Riwayat timeline pelacakan kargo.
     */
    public function index(Request $request, Shipment $shipment): JsonResponse
    {
        $user = $request->user();

        if ($user->isCustomer() && $shipment->customer_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $timeline = $this->trackingService->getTimeline($shipment);

        return response()->json([
            'success' => true,
            'data' => $timeline,
        ], 200);
    }

    /**
     * Merekam milestone/event pelacakan baru.
     */
    public function store(StoreTrackingEventRequest $request, Shipment $shipment): JsonResponse
    {
        $user = $request->user();

        if ($user->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: Customers cannot record tracking events.',
            ], 403);
        }

        try {
            $event = $this->trackingService->recordEvent($shipment, $request->validated(), $user);

            return response()->json([
                'success' => true,
                'message' => 'Tracking event recorded successfully.',
                'data' => $event,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
