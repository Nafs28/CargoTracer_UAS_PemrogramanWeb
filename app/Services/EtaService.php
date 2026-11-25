<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\ShipmentNotification;
use Carbon\Carbon;

class EtaService
{
    /**
     * Get active delay thresholds from config/cargotrace.php.
     */
    public function getThresholds(): array
    {
        return [
            'low' => (float) config('cargotrace.delay_thresholds.low', 6.0),
            'medium' => (float) config('cargotrace.delay_thresholds.medium', 24.0),
        ];
    }

    /**
     * Calculate delay status and duration for a shipment.
     * Formula: Delta t = Estimated Arrival - Expected Arrival
     */
    public function calculateDelay(Shipment $shipment): array
    {
        $thresholds = $this->getThresholds();

        if (!$shipment->expected_arrival) {
            return [
                'is_delayed' => false,
                'delay_hours' => 0.0,
                'severity' => null,
                'thresholds' => $thresholds,
            ];
        }

        $expectedArrival = Carbon::parse($shipment->expected_arrival);

        $referenceTime = $shipment->estimated_arrival
            ? Carbon::parse($shipment->estimated_arrival)
            : Carbon::now();

        $diffInSeconds = $expectedArrival->diffInSeconds($referenceTime, false);
        $diffInHours = round($diffInSeconds / 3600, 2);

        if ($diffInHours > 0) {
            return [
                'is_delayed' => true,
                'delay_hours' => $diffInHours,
                'severity' => $this->determineSeverity($diffInHours),
                'thresholds' => $thresholds,
            ];
        }

        return [
            'is_delayed' => false,
            'delay_hours' => 0.0,
            'severity' => null,
            'thresholds' => $thresholds,
        ];
    }

    /**
     * Determine severity level using configurable thresholds in config/cargotrace.php.
     */
    public function determineSeverity(float $delayHours): string
    {
        $thresholds = $this->getThresholds();

        if ($delayHours <= $thresholds['low']) {
            return 'LOW';
        }
        if ($delayHours <= $thresholds['medium']) {
            return 'MEDIUM';
        }

        return 'HIGH';
    }

    /**
     * Recalculate and persist delay attributes on a shipment.
     */
    public function updateShipmentDelay(Shipment $shipment, ?string $reason = null): Shipment
    {
        $wasDelayed = (bool) $shipment->is_delayed;
        $delayData = $this->calculateDelay($shipment);

        $shipment->is_delayed = $delayData['is_delayed'];
        $shipment->delay_hours = $delayData['delay_hours'];
        $shipment->delay_severity = $delayData['severity'];

        if ($delayData['is_delayed']) {
            if ($reason !== null) {
                $shipment->delay_reason = $reason;
            } elseif (empty($shipment->delay_reason)) {
                $shipment->delay_reason = "Estimated arrival exceeds expected schedule by {$delayData['delay_hours']} hours.";
            }
        } else {
            $shipment->delay_reason = null;
        }

        $shipment->save();

        // Notify customer when shipment newly becomes delayed
        if (!$wasDelayed && $shipment->is_delayed && $shipment->customer_id) {
            ShipmentNotification::create([
                'user_id' => $shipment->customer_id,
                'shipment_id' => $shipment->id,
                'title' => "Delay Alert: {$shipment->shipment_number}",
                'message' => "Shipment is delayed by {$shipment->delay_hours} hours (Severity: {$shipment->delay_severity}).",
                'type' => 'DELAY_ALERT',
            ]);
        }

        return $shipment;
    }

    /**
     * Recalculate delays across all active (non-terminal) shipments.
     */
    public function recalculateAllDelays(): int
    {
        $activeShipments = Shipment::whereNotIn('current_status', Shipment::TERMINAL_STATUSES)->get();
        $updatedCount = 0;

        foreach ($activeShipments as $shipment) {
            $this->updateShipmentDelay($shipment);
            $updatedCount++;
        }

        return $updatedCount;
    }
}
