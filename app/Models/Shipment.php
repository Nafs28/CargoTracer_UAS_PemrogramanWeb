<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipment extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_CREATED = 'CREATED';
    const STATUS_BOOKED = 'BOOKED';
    const STATUS_PICKED_UP = 'PICKED_UP';
    const STATUS_IN_TRANSIT = 'IN_TRANSIT';
    const STATUS_DELIVERED = 'DELIVERED';
    const STATUS_CANCELLED = 'CANCELLED';

    const STATUSES = [
        self::STATUS_CREATED,
        self::STATUS_BOOKED,
        self::STATUS_PICKED_UP,
        self::STATUS_IN_TRANSIT,
        self::STATUS_DELIVERED,
        self::STATUS_CANCELLED,
    ];

    const TERMINAL_STATUSES = [
        self::STATUS_DELIVERED,
        self::STATUS_CANCELLED,
    ];

    protected $fillable = [
        'shipment_number',
        'customer_id',
        'carrier_id',
        'origin_id',
        'destination_id',
        'current_status',
        'cargo_type',
        'weight',
        'volume',
        'description',
        'expected_departure',
        'expected_arrival',
        'estimated_arrival',
        'is_delayed',
        'delay_hours',
        'delay_severity',
        'delay_reason',
    ];

    protected function casts(): array
    {
        return [
            'expected_departure' => 'datetime',
            'expected_arrival' => 'datetime',
            'estimated_arrival' => 'datetime',
            'is_delayed' => 'boolean',
            'weight' => 'decimal:2',
            'volume' => 'decimal:2',
            'delay_hours' => 'decimal:2',
        ];
    }

    // Relationships
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    public function origin()
    {
        return $this->belongsTo(Location::class, 'origin_id');
    }

    public function destination()
    {
        return $this->belongsTo(Location::class, 'destination_id');
    }

    public function items()
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function trackingEvents()
    {
        return $this->hasMany(TrackingEvent::class)->orderBy('event_time', 'asc');
    }

    public function exceptions()
    {
        return $this->hasMany(ShipmentException::class);
    }

    public function notifications()
    {
        return $this->hasMany(ShipmentNotification::class);
    }

    // Helper methods
    public function getLatestTrackingEvent()
    {
        return $this->trackingEvents()->latest('event_time')->first();
    }

    public function isTerminalState(): bool
    {
        return in_array($this->current_status, self::TERMINAL_STATUSES);
    }

    public function getOpenExceptionsCount(): int
    {
        return $this->exceptions()->where('status', '!=', 'RESOLVED')->count();
    }
}
