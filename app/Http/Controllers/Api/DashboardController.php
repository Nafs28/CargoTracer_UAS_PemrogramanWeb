<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Get operational summary statistics.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $stats = $this->dashboardService->getStats($user);
        $recent = $this->dashboardService->getRecentActivity(10, $user);

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'recent_activity' => $recent,
            ],
        ], 200);
    }

    /**
     * Get status distribution for chart.
     */
    public function shipmentsByStatus(Request $request): JsonResponse
    {
        $data = $this->dashboardService->getShipmentsByStatus($request->user());

        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200);
    }

    /**
     * Get carrier breakdown for chart.
     */
    public function shipmentsByCarrier(Request $request): JsonResponse
    {
        $data = $this->dashboardService->getShipmentsByCarrier($request->user());

        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200);
    }

    /**
     * Get delay trend over time.
     */
    public function delayTrend(Request $request): JsonResponse
    {
        $data = $this->dashboardService->getDelayTrend($request->user());

        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200);
    }
}
