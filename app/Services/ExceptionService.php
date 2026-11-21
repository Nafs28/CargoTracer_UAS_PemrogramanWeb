<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\ShipmentNotification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ExceptionService
{
    protected AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Report a new operational incident/exception.
     */
    public function reportException(Shipment $shipment, array $data, User $user): ShipmentException
    {
        return DB::transaction(function () use ($shipment, $data, $user) {
            $exception = ShipmentException::create([
                'shipment_id' => $shipment->id,
                'exception_type' => $data['exception_type'],
                'severity' => $data['severity'] ?? ShipmentException::SEVERITY_LOW,
                'status' => ShipmentException::STATUS_OPEN,
                'notes' => $data['notes'] ?? null,
                'reported_by' => $user->id,
            ]);

            // Notify customer if shipment has a customer
            if ($shipment->customer_id) {
                ShipmentNotification::create([
                    'user_id' => $shipment->customer_id,
                    'shipment_id' => $shipment->id,
                    'title' => "Incident Reported on {$shipment->shipment_number}",
                    'message' => "An issue [{$exception->exception_type}] with {$exception->severity} severity has been flagged.",
                    'type' => 'warning',
                ]);
            }

            $this->auditService->log($user, 'REPORT_EXCEPTION', $exception, null, $exception->toArray());

            return $exception->load(['shipment', 'reporter']);
        });
    }

    /**
     * Move exception to INVESTIGATING.
     */
    public function investigateException(ShipmentException $exception, array $data, User $user): ShipmentException
    {
        return DB::transaction(function () use ($exception, $data, $user) {
            $oldValues = $exception->toArray();

            $exception->status = ShipmentException::STATUS_INVESTIGATING;
            if (isset($data['notes'])) {
                $exception->notes = ($exception->notes ? $exception->notes . "\n" : '') . "[INVESTIGATION " . Carbon::now()->toDateTimeString() . " by {$user->name}]: " . $data['notes'];
            }
            if (isset($data['severity'])) {
                $exception->severity = $data['severity'];
            }
            $exception->save();

            $this->auditService->log($user, 'INVESTIGATE_EXCEPTION', $exception, $oldValues, $exception->toArray());

            return $exception->fresh();
        });
    }

    /**
     * Resolve exception.
     */
    public function resolveException(ShipmentException $exception, array $data, User $user): ShipmentException
    {
        return DB::transaction(function () use ($exception, $data, $user) {
            $oldValues = $exception->toArray();

            $exception->status = ShipmentException::STATUS_RESOLVED;
            $exception->resolved_at = Carbon::now();
            if (isset($data['notes'])) {
                $exception->notes = ($exception->notes ? $exception->notes . "\n" : '') . "[RESOLVED " . Carbon::now()->toDateTimeString() . " by {$user->name}]: " . $data['notes'];
            }
            $exception->save();

            // Notify customer
            if ($exception->shipment && $exception->shipment->customer_id) {
                ShipmentNotification::create([
                    'user_id' => $exception->shipment->customer_id,
                    'shipment_id' => $exception->shipment_id,
                    'title' => "Incident Resolved on {$exception->shipment->shipment_number}",
                    'message' => "The issue [{$exception->exception_type}] has been marked as resolved.",
                    'type' => 'success',
                ]);
            }

            $this->auditService->log($user, 'RESOLVE_EXCEPTION', $exception, $oldValues, $exception->toArray());

            return $exception->fresh();
        });
    }

    /**
     * Update exception status (generic transition).
     */
    public function updateStatus(ShipmentException $exception, string $newStatus, ?string $notes, User $user): ShipmentException
    {
        $newStatus = strtoupper($newStatus);

        if ($newStatus === ShipmentException::STATUS_INVESTIGATING) {
            return $this->investigateException($exception, ['notes' => $notes], $user);
        }
        if ($newStatus === ShipmentException::STATUS_RESOLVED) {
            return $this->resolveException($exception, ['notes' => $notes], $user);
        }

        $exception->status = $newStatus;
        if ($notes) {
            $exception->notes = $notes;
        }
        $exception->save();

        return $exception;
    }

    /**
     * Get all active / open exceptions requiring operational attention.
     */
    public function getOpenExceptions(): Collection
    {
        return ShipmentException::with(['shipment.carrier', 'reporter'])
            ->whereIn('status', [ShipmentException::STATUS_OPEN, ShipmentException::STATUS_INVESTIGATING])
            ->latest()
            ->get();
    }
}
