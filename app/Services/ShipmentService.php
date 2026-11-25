<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\ShipmentNotification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ShipmentService
{
    protected AuditService $auditService;
    protected EtaService $etaService;

    const VALID_TRANSITIONS = [
        'CREATED' => ['BOOKED', 'CANCELLED'],
        'BOOKED' => ['PICKED_UP', 'CANCELLED'],
        'PICKED_UP' => ['IN_TRANSIT', 'CANCELLED'],
        'IN_TRANSIT' => ['DELIVERED', 'CANCELLED'],
        'DELIVERED' => [],
        'CANCELLED' => [],
    ];

    public function __construct(AuditService $auditService, EtaService $etaService)
    {
        $this->auditService = $auditService;
        $this->etaService = $etaService;
    }

    /**
     * Generate unique shipment number format: CT-YYYYMMDD-XXXXX
     */
    public function generateShipmentNumber(): string
    {
        $date = Carbon::now()->format('Ymd');
        do {
            $random = strtoupper(bin2hex(random_bytes(3))); // 6 hex characters
            $number = "CT-{$date}-{$random}";
        } while (Shipment::where('shipment_number', $number)->exists());

        return $number;
    }

    /**
     * Create a new shipment with transaction, audit log, and items.
     */
    public function createShipment(array $data, User $user): Shipment
    {
        return DB::transaction(function () use ($data, $user) {
            $data['shipment_number'] = $data['shipment_number'] ?? $this->generateShipmentNumber();
            $data['current_status'] = Shipment::STATUS_CREATED;

            // If customer creating (or customer specified)
            if ($user->isCustomer()) {
                $data['customer_id'] = $user->id;
            }

            $items = $data['items'] ?? [];
            unset($data['items']);

            $shipment = Shipment::create($data);

            // Create shipment items if provided
            if (!empty($items)) {
                foreach ($items as $item) {
                    $shipment->items()->create($item);
                }
            }

            // Calculate initial delay state
            $this->etaService->updateShipmentDelay($shipment);

            // Audit log
            $this->auditService->log($user, 'CREATE_SHIPMENT', $shipment, null, $shipment->toArray());

            return $shipment->load(['customer', 'carrier', 'origin', 'destination', 'items']);
        });
    }

    /**
     * Update an existing shipment.
     */
    public function updateShipment(Shipment $shipment, array $data, User $user): Shipment
    {
        return DB::transaction(function () use ($shipment, $data, $user) {
            $oldValues = $shipment->toArray();

            // Prevent updating terminal shipments
            if ($shipment->isTerminalState() && !isset($data['allow_override'])) {
                throw new InvalidArgumentException("Shipment is in terminal state ({$shipment->current_status}) and cannot be modified.");
            }

            // Cannot directly change current_status through general update (must use transitionStatus)
            unset($data['current_status']);
            unset($data['shipment_number']);

            $shipment->update($data);

            // Recalculate delay if arrival dates changed
            if (isset($data['expected_arrival']) || isset($data['estimated_arrival'])) {
                $this->etaService->updateShipmentDelay($shipment);
            }

            $this->auditService->log($user, 'UPDATE_SHIPMENT', $shipment, $oldValues, $shipment->toArray());

            return $shipment->fresh(['customer', 'carrier', 'origin', 'destination', 'items']);
        });
    }

    /**
     * Transition shipment status using strict state machine.
     */
    public function transitionStatus(Shipment $shipment, string $newStatus, User $user): Shipment
    {
        $newStatus = strtoupper($newStatus);

        if (!in_array($newStatus, Shipment::STATUSES)) {
            throw new InvalidArgumentException("Unknown shipment status: {$newStatus}");
        }

        $currentStatus = $shipment->current_status;

        if ($shipment->isTerminalState()) {
            throw new InvalidArgumentException("Cannot change status: Shipment is already locked in terminal state ({$currentStatus}).");
        }

        $allowedTransitions = self::VALID_TRANSITIONS[$currentStatus] ?? [];

        if (!in_array($newStatus, $allowedTransitions)) {
            $allowedList = implode(', ', $allowedTransitions);
            throw new InvalidArgumentException("Illegal status transition from [{$currentStatus}] to [{$newStatus}]. Allowed next states: [{$allowedList}].");
        }

        return DB::transaction(function () use ($shipment, $currentStatus, $newStatus, $user) {
            $oldValues = ['current_status' => $currentStatus];
            $shipment->current_status = $newStatus;
            $shipment->save();

            // Auto-recalculate delay on status changes
            $this->etaService->updateShipmentDelay($shipment);

            // In-app notification for customer
            if ($shipment->customer_id) {
                ShipmentNotification::create([
                    'user_id' => $shipment->customer_id,
                    'shipment_id' => $shipment->id,
                    'title' => "Status Updated: {$shipment->shipment_number}",
                    'message' => "Shipment status changed from {$currentStatus} to {$newStatus}.",
                    'type' => 'STATUS_UPDATE',
                ]);
            }

            $this->auditService->log($user, 'TRANSITION_STATUS', $shipment, $oldValues, ['current_status' => $newStatus]);

            return $shipment->fresh();
        });
    }

    /**
     * Special Administrator override for terminal status (logged in audit_logs).
     */
    public function overrideStatus(Shipment $shipment, string $newStatus, string $reason, User $admin): Shipment
    {
        if (!$admin->isAdmin()) {
            throw new InvalidArgumentException('Only Administrator can perform a status override.');
        }

        $newStatus = strtoupper($newStatus);
        if (!in_array($newStatus, Shipment::STATUSES)) {
            throw new InvalidArgumentException("Unknown shipment status: {$newStatus}");
        }

        return DB::transaction(function () use ($shipment, $newStatus, $reason, $admin) {
            $oldStatus = $shipment->current_status;
            $shipment->current_status = $newStatus;
            $shipment->save();

            $this->etaService->updateShipmentDelay($shipment);

            $this->auditService->log($admin, 'ADMIN_OVERRIDE', $shipment, [
                'current_status' => $oldStatus,
            ], [
                'current_status' => $newStatus,
                'reason' => $reason,
            ]);

            return $shipment->fresh();
        });
    }

    /**
     * Soft delete shipment.
     */
    public function deleteShipment(Shipment $shipment, User $user): bool
    {
        return DB::transaction(function () use ($shipment, $user) {
            $oldValues = $shipment->toArray();
            $deleted = $shipment->delete();

            $this->auditService->log($user, 'DELETE_SHIPMENT', $shipment, $oldValues, ['deleted_at' => Carbon::now()]);

            return $deleted;
        });
    }

    /**
     * Get scoped and filtered shipments list.
     */
    public function getShipmentsForUser(User $user, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Shipment::with(['customer', 'carrier', 'origin', 'destination']);

        // Tenant Isolation: Customer only sees own shipments
        if ($user->isCustomer()) {
            $query->where('customer_id', $user->id);
        }

        // Search multi-parameter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('shipment_number', 'like', "%{$search}%")
                  ->orWhere('cargo_type', 'like', "%{$search}%")
                  ->orWhereHas('carrier', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter status
        if (!empty($filters['status'])) {
            $query->where('current_status', strtoupper($filters['status']));
        }

        // Filter carrier, origin, destination
        if (!empty($filters['carrier_id'])) {
            $query->where('carrier_id', $filters['carrier_id']);
        }
        if (!empty($filters['origin_id'])) {
            $query->where('origin_id', $filters['origin_id']);
        }
        if (!empty($filters['destination_id'])) {
            $query->where('destination_id', $filters['destination_id']);
        }

        // Filter is_delayed
        if (isset($filters['is_delayed']) && $filters['is_delayed'] !== '') {
            $query->where('is_delayed', filter_var($filters['is_delayed'], FILTER_VALIDATE_BOOLEAN));
        }

        // Date range filter
        if (!empty($filters['date_from'])) {
            $query->whereDate('expected_departure', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('expected_arrival', '<=', $filters['date_to']);
        }

        // Sorting (whitelist columns to prevent SQL injection)
        $allowedSort = ['created_at', 'shipment_number', 'expected_departure', 'expected_arrival', 'current_status', 'delay_hours'];
        $sortBy = in_array($filters['sort_by'] ?? '', $allowedSort) ? $filters['sort_by'] : 'created_at';
        $sortDir = strtolower($filters['sort_dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($sortBy, $sortDir)->paginate($perPage);
    }
}
