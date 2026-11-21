<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    const TYPE_PORT = 'port';
    const TYPE_WAREHOUSE = 'warehouse';
    const TYPE_HUB = 'hub';
    const TYPE_CUSTOMS = 'customs';
    const TYPE_AIRPORT = 'airport';
    const TYPE_DEPOT = 'depot';

    const TYPES = [
        self::TYPE_PORT,
        self::TYPE_WAREHOUSE,
        self::TYPE_HUB,
        self::TYPE_CUSTOMS,
        self::TYPE_AIRPORT,
        self::TYPE_DEPOT,
    ];

    protected $fillable = [
        'name',
        'code',
        'type',
        'address',
        'city',
        'country',
        'latitude',
        'longitude',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function shipmentsAsOrigin()
    {
        return $this->hasMany(Shipment::class, 'origin_id');
    }

    public function shipmentsAsDestination()
    {
        return $this->hasMany(Shipment::class, 'destination_id');
    }

    public function trackingEvents()
    {
        return $this->hasMany(TrackingEvent::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
