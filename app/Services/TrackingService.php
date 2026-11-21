<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TrackingService
{
    protected AuditService $auditService;
    protected EtaService $etaService;

    public function __construct(AuditService $auditService, EtaService $etaService)
    {
        $this->auditService = $auditService;
        $this->etaService = $etaService;
    }

    /**
     * Record an append-only tracking milestone with chronological validation.
     */
    public function recordEvent(Shipment $shipment, array $data, User $user): TrackingEvent
    {
        $eventTime = Carbon::parse($data['event_time'] ?? Carbon::now());

        // Validate chronological order: new event must not have timestamp older than latest event
        $latestEvent = $shipment->trackingEvents()->latest('event_time')->first();
        if ($latestEvent && $eventTime->lt(Carbon::parse($latestEvent->event_time))) {
            throw new InvalidArgumentException(
                "Chronological integrity violation: Event timestamp [{$eventTime}] cannot precede the latest event timestamp [{$latestEvent->event_time}]."
            );
        }

        return DB::transaction(function () use ($shipment, $data, $eventTime, $user) {
            $trackingEvent = TrackingEvent::create([
                'shipment_id' => $shipment->id,
                'location_id' => $data['location_id'] ?? null,
                'status_type' => $data['status_type'] ?? $shipment->current_status,
                'event_time' => $eventTime,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'description' => $data['description'] ?? 'Status update',
                'created_by' => $user->id,
            ]);

            // Sync shipment status if status_type matches a lifecycle state
            $statusCandidate = strtoupper($trackingEvent->status_type);
            if (in_array($statusCandidate, Shipment::STATUSES) && $statusCandidate !== $shipment->current_status) {
                // If legal transition, update current_status
                $allowed = ShipmentService::VALID_TRANSITIONS[$shipment->current_status] ?? [];
                if (in_array($statusCandidate, $allowed)) {
                    $shipment->current_status = $statusCandidate;
                    $shipment->save();
                }
            }

            // Recalculate delay state
            $this->etaService->updateShipmentDelay($shipment);

            $this->auditService->log($user, 'RECORD_TRACKING_EVENT', $trackingEvent, null, $trackingEvent->toArray());

            return $trackingEvent->load(['location', 'creator']);
        });
    }

    /**
     * Get ordered historical timeline for a shipment.
     */
    public function getTimeline(Shipment $shipment): Collection
    {
        return $shipment->trackingEvents()
            ->with(['location', 'creator'])
            ->orderBy('event_time', 'asc')
            ->get();
    }

    /**
     * Get the dynamic current location (from latest valid event with coordinates, or destination/origin).
     */
    public function getCurrentLocation(Shipment $shipment): ?array
    {
        $latestWithCoords = $shipment->trackingEvents()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->latest('event_time')
            ->first();

        if ($latestWithCoords) {
            return [
                'latitude' => (float) $latestWithCoords->latitude,
                'longitude' => (float) $latestWithCoords->longitude,
                'location_name' => $latestWithCoords->location ? $latestWithCoords->location->name : 'In Transit Coordinates',
                'event_time' => $latestWithCoords->event_time,
                'status_type' => $latestWithCoords->status_type,
            ];
        }

        // Fallback to origin coordinates
        if ($shipment->origin && $shipment->origin->latitude) {
            return [
                'latitude' => (float) $shipment->origin->latitude,
                'longitude' => (float) $shipment->origin->longitude,
                'location_name' => $shipment->origin->name,
                'event_time' => $shipment->created_at,
                'status_type' => 'ORIGIN',
            ];
        }

        return null;
    }
}
