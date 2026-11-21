<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentException extends Model
{
    use HasFactory;

    const TYPE_CUSTOMS_HOLD = 'CUSTOMS_HOLD';
    const TYPE_WEATHER_DELAY = 'WEATHER_DELAY';
    const TYPE_PORT_CONGESTION = 'PORT_CONGESTION';
    const TYPE_DAMAGED = 'DAMAGED';
    const TYPE_MISROUTED = 'MISROUTED';
    const TYPE_DOCUMENTATION_ISSUE = 'DOCUMENTATION_ISSUE';

    const TYPES = [
        self::TYPE_CUSTOMS_HOLD,
        self::TYPE_WEATHER_DELAY,
        self::TYPE_PORT_CONGESTION,
        self::TYPE_DAMAGED,
        self::TYPE_MISROUTED,
        self::TYPE_DOCUMENTATION_ISSUE,
    ];

    const SEVERITY_LOW = 'LOW';
    const SEVERITY_MEDIUM = 'MEDIUM';
    const SEVERITY_HIGH = 'HIGH';

    const SEVERITIES = [
        self::SEVERITY_LOW,
        self::SEVERITY_MEDIUM,
        self::SEVERITY_HIGH,
    ];

    const STATUS_OPEN = 'OPEN';
    const STATUS_INVESTIGATING = 'INVESTIGATING';
    const STATUS_RESOLVED = 'RESOLVED';

    const STATUSES = [
        self::STATUS_OPEN,
        self::STATUS_INVESTIGATING,
        self::STATUS_RESOLVED,
    ];

    protected $fillable = [
        'shipment_id',
        'exception_type',
        'severity',
        'status',
        'notes',
        'reported_by',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    // Relationships
    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
