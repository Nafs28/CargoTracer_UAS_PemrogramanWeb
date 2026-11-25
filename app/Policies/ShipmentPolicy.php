<?php

namespace App\Policies;

use App\Models\Shipment;
use App\Models\User;

class ShipmentPolicy
{
    /**
     * Any authenticated user can list shipments (scoped by service).
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Admin & Operator can view all shipments; Customer can only view their own.
     */
    public function view(User $user, Shipment $shipment): bool
    {
        if ($user->isAdmin() || $user->isOperator()) {
            return true;
        }

        return (int) $shipment->customer_id === (int) $user->id;
    }

    /**
     * All authenticated roles can create shipments (Customer creates for self).
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Only Admin & Operator can update shipment details or transition status.
     */
    public function update(User $user, Shipment $shipment): bool
    {
        return $user->isAdmin() || $user->isOperator();
    }

    /**
     * Only Admin can soft-delete a shipment.
     */
    public function delete(User $user, Shipment $shipment): bool
    {
        return $user->isAdmin();
    }

    /**
     * Only Admin can override a terminal shipment status.
     */
    public function overrideStatus(User $user, Shipment $shipment): bool
    {
        return $user->isAdmin();
    }
}
