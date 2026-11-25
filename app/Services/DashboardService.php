<?php

namespace App\Services;

use App\Models\Carrier;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\TrackingEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Compute actual aggregated live operational metrics (Not mock data).
     */
    public function getStats(?User $user = null): array
    {
        $shipmentQuery = Shipment::query();

        // Customer tenant isolation
        if ($user && $user->isCustomer()) {
            $shipmentQuery->where('customer_id', $user->id);
        }

        $totalShipments = (clone $shipmentQuery)->count();
        $inTransitShipments = (clone $shipmentQuery)->where('current_status', Shipment::STATUS_IN_TRANSIT)->count();
        $deliveredShipments = (clone $shipmentQuery)->where('current_status', Shipment::STATUS_DELIVERED)->count();
        $delayedShipments = (clone $shipmentQuery)->where('is_delayed', true)->count();
        
        $onTimeShipments = max(0, $totalShipments - $delayedShipments);
        $onTimeRatio = $totalShipments > 0 ? round(($onTimeShipments / $totalShipments) * 100, 1) : 100.0;
        $delayedRatio = $totalShipments > 0 ? round(($delayedShipments / $totalShipments) * 100, 1) : 0.0;

        // Open Exceptions
        $exceptionQuery = ShipmentException::whereIn('status', [ShipmentException::STATUS_OPEN, ShipmentException::STATUS_INVESTIGATING]);
        if ($user && $user->isCustomer()) {
            $exceptionQuery->whereHas('shipment', function ($q) use ($user) {
                $q->where('customer_id', $user->id);
            });
        }
        $openExceptions = $exceptionQuery->count();

        // Active carriers count
        $activeCarriers = Carrier::where('is_active', true)->count();

        return [
            'total_shipments' => $totalShipments,
            'in_transit' => $inTransitShipments,
            'delivered' => $deliveredShipments,
            'delayed' => $delayedShipments,
            'on_time_ratio' => $onTimeRatio,
            'delayed_ratio' => $delayedRatio,
            'open_exceptions' => $openExceptions,
            'active_carriers' => $activeCarriers,
        ];
    }

    /**
     * Distribution of shipments by status for Chart.js doughnut.
     */
    public function getShipmentsByStatus(?User $user = null): array
    {
        $query = Shipment::select('current_status', DB::raw('count(*) as count'));

        if ($user && $user->isCustomer()) {
            $query->where('customer_id', $user->id);
        }

        $records = $query->groupBy('current_status')->pluck('count', 'current_status')->toArray();

        $labels = [];
        $data = [];

        foreach (Shipment::STATUSES as $status) {
            $labels[] = $status;
            $data[] = $records[$status] ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Distribution of shipments by carrier for Chart.js bar chart.
     */
    public function getShipmentsByCarrier(?User $user = null): array
    {
        $carriers = Carrier::withCount(['shipments' => function ($q) use ($user) {
            if ($user && $user->isCustomer()) {
                $q->where('customer_id', $user->id);
            }
        }])->get()->filter(fn ($carrier) => $carrier->shipments_count > 0)->values();

        return [
            'labels' => $carriers->pluck('name')->toArray(),
            'data' => $carriers->pluck('shipments_count')->toArray(),
        ];
    }

    /**
     * 30-day delay trend data for Chart.js line chart.
     */
    public function getDelayTrend(?User $user = null): array
    {
        $labels = [];
        $delayedData = [];
        $onTimeData = [];

        // Build last 14 days interval for readability
        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::now()->subDays($i)->format('d M');

            $dayQuery = Shipment::whereDate('created_at', '<=', $date);
            if ($user && $user->isCustomer()) {
                $dayQuery->where('customer_id', $user->id);
            }

            $delayed = (clone $dayQuery)->where('is_delayed', true)->count();
            $total = (clone $dayQuery)->count();
            $onTime = max(0, $total - $delayed);

            $delayedData[] = $delayed;
            $onTimeData[] = $onTime;
        }

        return [
            'labels' => $labels,
            'delayed' => $delayedData,
            'on_time' => $onTimeData,
        ];
    }

    /**
     * Recent tracking events stream.
     */
    public function getRecentActivity(int $limit = 10, ?User $user = null)
    {
        $query = TrackingEvent::with(['shipment.carrier', 'location', 'creator'])
            ->latest('event_time');

        if ($user && $user->isCustomer()) {
            $query->whereHas('shipment', function ($q) use ($user) {
                $q->where('customer_id', $user->id);
            });
        }

        return $query->take($limit)->get();
    }
}
