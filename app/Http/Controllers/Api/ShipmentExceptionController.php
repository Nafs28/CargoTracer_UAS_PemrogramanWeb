<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Exception\StoreExceptionRequest;
use App\Http\Requests\Exception\UpdateExceptionRequest;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Services\ExceptionService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShipmentExceptionController extends Controller
{
    protected ExceptionService $exceptionService;

    public function __construct(ExceptionService $exceptionService)
    {
        $this->exceptionService = $exceptionService;
    }

    /**
     * Menampilkan kendala kargo.
     */
    public function index(Request $request, ?Shipment $shipment = null): JsonResponse
    {
        $user = $request->user();

        if ($shipment) {
            if ($user->isCustomer() && $shipment->customer_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
            }
            $exceptions = $shipment->exceptions()->with(['reporter'])->latest()->get();
        } else {
            if ($user->isCustomer()) {
                return response()->json(['success' => false, 'message' => 'Akses terlarang.'], 403);
            }
            $exceptions = $this->exceptionService->getOpenExceptions();
        }

        return response()->json([
            'success' => true,
            'data' => $exceptions,
        ], 200);
    }

    /**
     * Melaporkan kendala baru pada kargo.
     */
    public function store(StoreExceptionRequest $request, Shipment $shipment): JsonResponse
    {
        $user = $request->user();

        if ($user->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => 'Terlarang: Pelanggan tidak dapat melaporkan kendala secara langsung.',
            ], 403);
        }

        try {
            $exception = $this->exceptionService->reportException($shipment, $request->validated(), $user);

            return response()->json([
                'success' => true,
                'message' => 'Laporan kendala operasional berhasil dibuat.',
                'data' => $exception,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Update status resolusi exception.
     */
    public function update(UpdateExceptionRequest $request, ShipmentException $exception): JsonResponse
    {
        $user = $request->user();

        if ($user->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses terlarang.',
            ], 403);
        }

        try {
            $status = $request->get('status', $exception->status);
            $notes = $request->get('notes');
            
            $updated = $this->exceptionService->updateStatus($exception, $status, $notes, $user);

            return response()->json([
                'success' => true,
                'message' => "Status kendala diperbarui menjadi {$updated->status}.",
                'data' => $updated,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
