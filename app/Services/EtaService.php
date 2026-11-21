<?php

namespace App\Services;

use App\Models\Shipment;
use Carbon\Carbon;

class EtaService
{
    /**
     * Calculate delay status and duration for a shipment.
     * Formula: Delta t = Estimated Arrival (or now if past expected) - Expected Arrival
     * If delta > 0 => is_delayed = true
     */
    public function calculateDelay(Shipment $shipment): array
    {
        if (!$shipment->expected_arrival) {
            return [
                'is_delayed' => false,
                'delay_hours' => 0.0,
                'severity' => null,
            ];
        }

        $expectedArrival = Carbon::parse($shipment->expected_arrival);
        
        // Use estimated_arrival if set; otherwise, if already delivered, use delivered event time, else compare with current time if in progress
        $referenceTime = $shipment->estimated_arrival 
            ? Carbon::parse($shipment->estimated_arrival) 
            : Carbon::now();

        // Calculate delta in hours (positive means referenceTime is after expectedArrival)
        $diffInSeconds = $expectedArrival->diffInSeconds($referenceTime, false);
        $diffInHours = round($diffInSeconds / 3600, 2);

        if ($diffInHours > 0) {
            $severity = $this->determineSeverity($diffInHours);
            return [
                'is_delayed' => true,
                'delay_hours' => $diffInHours,
                'severity' => $severity,
            ];
        }

        return [
            'is_delayed' => false,
            'delay_hours' => 0.0,
            'severity' => null,
        ];
    }

    /**
     * Determine severity level based on delayed hours.
     * LOW: 0 to 6 hours
     * MEDIUM: > 6 to 24 hours
     * HIGH: > 24 hours
     */
    public function determineSeverity(float $delayHours): string
    {
        if ($delayHours <= 6.0) {
            return 'LOW';
        }
        if ($delayHours <= 24.0) {
            return 'MEDIUM';
        }
        return 'HIGH';
    }

    /**
     * Recalculate and update delay attributes on a shipment.
     */
    public function updateShipmentDelay(Shipment $shipment): Shipment
    {
        $delayData = $this->calculateDelay($shipment);
        $shipment->is_delayed = $delayData['is_delayed'];
        $shipment->delay_hours = $delayData['delay_hours'];
        $shipment->delay_severity = $delayData['severity'];
        $shipment->save();

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
