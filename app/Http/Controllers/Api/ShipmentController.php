<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipment\StoreShipmentRequest;
use App\Http\Requests\Shipment\UpdateShipmentRequest;
use App\Http\Requests\Shipment\UpdateStatusRequest;
use App\Models\Shipment;
use App\Services\ShipmentService;
use App\Services\TrackingService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    protected ShipmentService $shipmentService;
    protected TrackingService $trackingService;

    public function __construct(ShipmentService $shipmentService, TrackingService $trackingService)
    {
        $this->shipmentService = $shipmentService;
        $this->trackingService = $trackingService;
    }

    /**
     * List data kargo (dengan filter & server-side pagination).
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 15);
        $filters = $request->only(['search', 'status', 'carrier_id', 'is_delayed', 'date_from', 'date_to']);

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
     * Membuat order kargo baru.
     */
    public function store(StoreShipmentRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $shipment = $this->shipmentService->createShipment($request->validated(), $user);

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
     * Detail lengkap kargo, rute, dan status.
     */
    public function show(Request $request, Shipment $shipment): JsonResponse
    {
        $user = $request->user();

        // Isolasi tenant customer
        if ($user->isCustomer() && $shipment->customer_id !== $user->id) {
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

        $currentLocation = $this->trackingService->getCurrentLocation($shipment);

        return response()->json([
            'success' => true,
            'data' => [
                'shipment' => $shipment,
                'current_location' => $currentLocation,
            ],
        ], 200);
    }

    /**
     * Memperbarui data kargo.
     */
    public function update(UpdateShipmentRequest $request, Shipment $shipment): JsonResponse
    {
        $user = $request->user();

        if ($user->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => 'Terlarang: Pelanggan tidak memiliki izin mengubah data pengiriman.',
            ], 403);
        }

        try {
            $updated = $this->shipmentService->updateShipment($shipment, $request->validated(), $user);

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
     * Menghapus / Soft-delete kargo (Khusus Admin).
     */
    public function destroy(Request $request, Shipment $shipment): JsonResponse
    {
        $user = $request->user();

        if (!$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Terlarang: Hanya Administrator yang berhak menghapus kargo.',
            ], 403);
        }

        $this->shipmentService->deleteShipment($shipment, $user);

        return response()->json([
            'success' => true,
            'message' => "Kargo {$shipment->shipment_number} berhasil dihapus.",
        ], 200);
    }

    /**
     * Transisi status via state machine.
     */
    public function updateStatus(UpdateStatusRequest $request, Shipment $shipment): JsonResponse
    {
        $user = $request->user();

        if ($user->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => 'Terlarang: Pelanggan tidak berhak mengubah status kargo.',
            ], 403);
        }

        try {
            $updated = $this->shipmentService->transitionStatus($shipment, $request->status, $user);

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
     * Data koordinat titik rute untuk Peta GIS Leaflet.js.
     */
    public function map(Request $request, Shipment $shipment): JsonResponse
    {
        $user = $request->user();

        if ($user->isCustomer() && $shipment->customer_id !== $user->id) {
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

        $current = $this->trackingService->getCurrentLocation($shipment);

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
                'current_position' => $current,
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
