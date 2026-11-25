<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipment\StoreShipmentRequest;
use App\Http\Requests\Shipment\UpdateShipmentRequest;
use App\Http\Requests\Shipment\UpdateStatusRequest;
use App\Models\Shipment;
use App\Services\EtaService;
use App\Services\ShipmentService;
use App\Services\TrackingService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ShipmentController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService,
        protected TrackingService $trackingService,
        protected EtaService $etaService
    ) {
    }

    /**
     * List shipments with filters, sorting, and server-side pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 15);
        $filters = $request->only([
            'search',
            'status',
            'carrier_id',
            'origin_id',
            'destination_id',
            'is_delayed',
            'date_from',
            'date_to',
            'sort_by',
            'sort_dir',
        ]);

        $shipments = $this->shipmentService->getShipmentsForUser($request->user(), $filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => $shipments->items(),
            'meta' => [
                'current_page' => $shipments->currentPage(),
                'last_page' => $shipments->lastPage(),
                'per_page' => $shipments->perPage(),
                'total' => $shipments->total(),
            ],
        ], 200);
    }

    /**
     * Create a new shipment order.
     */
    public function store(StoreShipmentRequest $request): JsonResponse
    {
        try {
            $shipment = $this->shipmentService->createShipment($request->validated(), $request->user());

            return response()->json([
                'success' => true,
                'message' => "Order kargo {$shipment->shipment_number} berhasil dibuat.",
                'data' => $shipment,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Show full shipment details, items, tracking timeline, and delay thresholds.
     */
    public function show(Request $request, Shipment $shipment): JsonResponse
    {
        if (!Gate::forUser($request->user())->allows('view', $shipment)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak: Anda tidak memiliki wewenang atas data kargo ini.',
            ], 403);
        }

        $shipment->load([
            'customer',
            'carrier',
            'origin',
            'destination',
            'items',
            'trackingEvents.location',
            'trackingEvents.creator',
            'exceptions.reporter',
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'shipment' => $shipment,
                'current_location' => $this->trackingService->getCurrentLocation($shipment),
                'delay_thresholds' => $this->etaService->getThresholds(),
            ],
        ], 200);
    }

    /**
     * Update shipment metadata (Admin & Operator).
     */
    public function update(UpdateShipmentRequest $request, Shipment $shipment): JsonResponse
    {
        if (!Gate::forUser($request->user())->allows('update', $shipment)) {
            return response()->json([
                'success' => false,
                'message' => 'Terlarang: Pelanggan tidak memiliki izin mengubah data pengiriman.',
            ], 403);
        }

        try {
            $updated = $this->shipmentService->updateShipment($shipment, $request->validated(), $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Data kargo berhasil diperbarui.',
                'data' => $updated,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Soft-delete shipment (Admin only).
     */
    public function destroy(Request $request, Shipment $shipment): JsonResponse
    {
        if (!Gate::forUser($request->user())->allows('delete', $shipment)) {
            return response()->json([
                'success' => false,
                'message' => 'Terlarang: Hanya Administrator yang berhak menghapus kargo.',
            ], 403);
        }

        $this->shipmentService->deleteShipment($shipment, $request->user());

        return response()->json([
            'success' => true,
            'message' => "Kargo {$shipment->shipment_number} berhasil dihapus.",
        ], 200);
    }

    /**
     * Transition shipment status via state machine (Admin & Operator).
     */
    public function updateStatus(UpdateStatusRequest $request, Shipment $shipment): JsonResponse
    {
        if (!Gate::forUser($request->user())->allows('update', $shipment)) {
            return response()->json([
                'success' => false,
                'message' => 'Terlarang: Pelanggan tidak berhak mengubah status kargo.',
            ], 403);
        }

        try {
            $updated = $this->shipmentService->transitionStatus($shipment, $request->status, $request->user());

            return response()->json([
                'success' => true,
                'message' => "Status kargo berhasil diubah menjadi {$updated->current_status}.",
                'data' => $updated,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Special Administrator override for terminal status (logged in audit_logs).
     */
    public function overrideStatus(Request $request, Shipment $shipment): JsonResponse
    {
        if (!Gate::forUser($request->user())->allows('overrideStatus', $shipment)) {
            return response()->json([
                'success' => false,
                'message' => 'Terlarang: Hanya Administrator yang dapat melakukan override status.',
            ], 403);
        }

        $validated = $request->validate([
            'status' => ['required', 'string'],
            'reason' => ['required', 'string', 'min:5'],
        ]);

        try {
            $updated = $this->shipmentService->overrideStatus(
                $shipment,
                $validated['status'],
                $validated['reason'],
                $request->user()
            );

            return response()->json([
                'success' => true,
                'message' => "Override status berhasil diubah menjadi {$updated->current_status}.",
                'data' => $updated,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Route coordinates and waypoints for Leaflet.js GIS map.
     */
    public function map(Request $request, Shipment $shipment): JsonResponse
    {
        if (!Gate::forUser($request->user())->allows('view', $shipment)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak: Data peta kargo tidak tersedia.',
            ], 403);
        }

        $shipment->load(['origin', 'destination']);
        $events = $shipment->trackingEvents()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('event_time', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'shipment_number' => $shipment->shipment_number,
                'current_status' => $shipment->current_status,
                'is_delayed' => $shipment->is_delayed,
                'origin' => [
                    'name' => $shipment->origin->name,
                    'latitude' => (float) $shipment->origin->latitude,
                    'longitude' => (float) $shipment->origin->longitude,
                ],
                'destination' => [
                    'name' => $shipment->destination->name,
                    'latitude' => (float) $shipment->destination->latitude,
                    'longitude' => (float) $shipment->destination->longitude,
                ],
                'current_position' => $this->trackingService->getCurrentLocation($shipment),
                'waypoints' => $events->map(fn ($e) => [
                    'latitude' => (float) $e->latitude,
                    'longitude' => (float) $e->longitude,
                    'status_type' => $e->status_type,
                    'event_time' => $e->event_time,
                    'description' => $e->description,
                ]),
            ],
        ], 200);
    }
}
