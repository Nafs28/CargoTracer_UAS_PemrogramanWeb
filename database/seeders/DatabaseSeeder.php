<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\ShipmentItem;
use App\Models\TrackingEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with comprehensive, realistic logistics data.
     */
    public function run(): void
    {
        // 1. Users & RBAC Roles
        $admin = User::firstOrCreate(['email' => 'admin@cargotrace.com'], [
            'name' => 'System Administrator',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+628110000001',
        ]);

        $operator = User::firstOrCreate(['email' => 'operator@cargotrace.com'], [
            'name' => 'Operations Officer',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'phone' => '+628110000002',
        ]);

        $customer = User::firstOrCreate(['email' => 'customer@cargotrace.com'], [
            'name' => 'PT Global Logistik Nusantara',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '+628110000003',
        ]);

        $customer2 = User::firstOrCreate(['email' => 'client@pacifictrade.com'], [
            'name' => 'Pacific Ocean Trade Ltd',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '+628110000004',
        ]);

        // 2. Carriers Master Data
        $carriersData = [
            ['name' => 'Maersk Line', 'code' => 'MAEU', 'contact_email' => 'ops@maersk.com', 'contact_phone' => '+4570000000', 'address' => 'Copenhagen, Denmark'],
            ['name' => 'Mediterranean Shipping Company (MSC)', 'code' => 'MSCU', 'contact_email' => 'ops@msc.com', 'contact_phone' => '+4122000000', 'address' => 'Geneva, Switzerland'],
            ['name' => 'CMA CGM Group', 'code' => 'CMDU', 'contact_email' => 'info@cma-cgm.com', 'contact_phone' => '+3348800000', 'address' => 'Marseille, France'],
            ['name' => 'Hapag-Lloyd', 'code' => 'HLCU', 'contact_email' => 'service@hlag.com', 'contact_phone' => '+4940000000', 'address' => 'Hamburg, Germany'],
            ['name' => 'Evergreen Marine Corp', 'code' => 'EGLV', 'contact_email' => 'dispatch@evergreen-marine.com', 'contact_phone' => '+8862200000', 'address' => 'Taoyuan, Taiwan'],
        ];

        $carrierModels = [];
        foreach ($carriersData as $c) {
            $carrierModels[] = Carrier::firstOrCreate(['code' => $c['code']], $c);
        }

        // 3. Locations (Major Logistics & Maritime Hubs)
        $locationsData = [
            ['name' => 'Tanjung Priok Port', 'code' => 'IDJKT', 'type' => 'port', 'city' => 'Jakarta', 'country' => 'Indonesia', 'latitude' => -6.1018, 'longitude' => 106.8833, 'address' => 'Jl. Pelabuhan Raya, Tanjung Priok'],
            ['name' => 'Tanjung Perak Port', 'code' => 'IDSUB', 'type' => 'port', 'city' => 'Surabaya', 'country' => 'Indonesia', 'latitude' => -7.2023, 'longitude' => 112.7303, 'address' => 'Jl. Perak Timur, Surabaya'],
            ['name' => 'Port of Singapore', 'code' => 'SGSIN', 'type' => 'port', 'city' => 'Singapore', 'country' => 'Singapore', 'latitude' => 1.2644, 'longitude' => 103.8220, 'address' => '7B Keppel Road, Tanjong Pagar'],
            ['name' => 'Port of Shanghai', 'code' => 'CNSHA', 'type' => 'port', 'city' => 'Shanghai', 'country' => 'China', 'latitude' => 31.2304, 'longitude' => 121.4737, 'address' => 'Yangshan Deep-Water Port'],
            ['name' => 'Port of Rotterdam', 'code' => 'NLRTM', 'type' => 'port', 'city' => 'Rotterdam', 'country' => 'Netherlands', 'latitude' => 51.9244, 'longitude' => 4.4777, 'address' => 'Wilhelminakade 909, Rotterdam'],
            ['name' => 'Port of Los Angeles', 'code' => 'USLAX', 'type' => 'port', 'city' => 'Los Angeles', 'country' => 'United States', 'latitude' => 33.7432, 'longitude' => -118.2673, 'address' => 'San Pedro Bay, Los Angeles'],
            ['name' => 'Jebel Ali Port', 'code' => 'AEJEA', 'type' => 'port', 'city' => 'Dubai', 'country' => 'United Arab Emirates', 'latitude' => 25.0113, 'longitude' => 55.0617, 'address' => 'Jebel Ali Free Zone, Dubai'],
            ['name' => 'Port of Busan', 'code' => 'KRPUS', 'type' => 'port', 'city' => 'Busan', 'country' => 'South Korea', 'latitude' => 35.1028, 'longitude' => 129.0403, 'address' => 'Jung-gu, Busan'],
            ['name' => 'Pelabuhan Belawan', 'code' => 'IDBLW', 'type' => 'port', 'city' => 'Medan', 'country' => 'Indonesia', 'latitude' => 3.7892, 'longitude' => 98.7150, 'address' => 'Bagan Deli, Medan Belawan, Sumatera Utara'],
            ['name' => 'Pelabuhan Makassar (Soekarno-Hatta)', 'code' => 'IDMAK', 'type' => 'port', 'city' => 'Makassar', 'country' => 'Indonesia', 'latitude' => -5.1195, 'longitude' => 119.4090, 'address' => 'Jl. Soekarno, Wajo, Makassar, Sulawesi Selatan'],
            ['name' => 'Pelabuhan Tanjung Emas', 'code' => 'IDSRG', 'type' => 'port', 'city' => 'Semarang', 'country' => 'Indonesia', 'latitude' => -6.9465, 'longitude' => 110.4283, 'address' => 'Tanjung Mas, Semarang Utara, Jawa Tengah'],
            ['name' => 'Pelabuhan Bitung', 'code' => 'IDBIT', 'type' => 'port', 'city' => 'Bitung', 'country' => 'Indonesia', 'latitude' => 1.4402, 'longitude' => 125.1915, 'address' => 'Bitung Timur, Maesa, Bitung, Sulawesi Utara'],
        ];

        $locModels = [];
        foreach ($locationsData as $loc) {
            $locModels[] = Location::firstOrCreate(['code' => $loc['code']], $loc);
        }

        // 4. Shipments Generator
        $shipmentDefs = [
            [
                'number' => 'CT-20261101-948F2A',
                'customer' => $customer,
                'carrier' => $carrierModels[0], // Maersk
                'origin' => $locModels[0], // Tanjung Priok
                'destination' => $locModels[4], // Rotterdam
                'status' => 'IN_TRANSIT',
                'cargo' => 'Electronics & Telecommunication Equipment',
                'weight' => 24500.00,
                'volume' => 68.50,
                'dep' => Carbon::now()->subDays(6),
                'arr' => Carbon::now()->addDays(8),
                'est' => Carbon::now()->addDays(9)->addHours(4), // Delayed by 28 hours (HIGH)
                'is_delayed' => true,
                'delay_hours' => 28.0,
                'severity' => 'HIGH',
            ],
            [
                'number' => 'CT-20261105-3B19E4',
                'customer' => $customer,
                'carrier' => $carrierModels[1], // MSC
                'origin' => $locModels[2], // Singapore
                'destination' => $locModels[5], // Los Angeles
                'status' => 'IN_TRANSIT',
                'cargo' => 'Automotive Spare Parts & Engines',
                'weight' => 42000.00,
                'volume' => 110.00,
                'dep' => Carbon::now()->subDays(4),
                'arr' => Carbon::now()->addDays(12),
                'est' => Carbon::now()->addDays(12)->addHours(8), // Delayed 8 hours (MEDIUM)
                'is_delayed' => true,
                'delay_hours' => 8.0,
                'severity' => 'MEDIUM',
            ],
            [
                'number' => 'CT-20261110-7F89CD',
                'customer' => $customer,
                'carrier' => $carrierModels[2], // CMA CGM
                'origin' => $locModels[3], // Shanghai
                'destination' => $locModels[0], // Tanjung Priok
                'status' => 'DELIVERED',
                'cargo' => 'Solar Panels & Inverters',
                'weight' => 18900.00,
                'volume' => 52.00,
                'dep' => Carbon::now()->subDays(14),
                'arr' => Carbon::now()->subDays(2),
                'est' => Carbon::now()->subDays(2),
                'is_delayed' => false,
                'delay_hours' => 0.0,
                'severity' => null,
            ],
            [
                'number' => 'CT-20261112-E5C12A',
                'customer' => $customer2,
                'carrier' => $carrierModels[3], // Hapag-Lloyd
                'origin' => $locModels[4], // Rotterdam
                'destination' => $locModels[6], // Dubai Jebel Ali
                'status' => 'PICKED_UP',
                'cargo' => 'Pharmaceutical Supplies & Vaccines',
                'weight' => 8500.00,
                'volume' => 24.00,
                'dep' => Carbon::now()->subDays(1),
                'arr' => Carbon::now()->addDays(7),
                'est' => Carbon::now()->addDays(7)->addHours(2), // Delayed 2 hours (LOW)
                'is_delayed' => true,
                'delay_hours' => 2.0,
                'severity' => 'LOW',
            ],
            [
                'number' => 'CT-20261115-88A9F1',
                'customer' => $customer2,
                'carrier' => $carrierModels[4], // Evergreen
                'origin' => $locModels[1], // Surabaya
                'destination' => $locModels[7], // Busan
                'status' => 'BOOKED',
                'cargo' => 'Raw Coffee Beans & Agricultural Goods',
                'weight' => 31200.00,
                'volume' => 84.00,
                'dep' => Carbon::now()->addDays(2),
                'arr' => Carbon::now()->addDays(10),
                'est' => Carbon::now()->addDays(10),
                'is_delayed' => false,
                'delay_hours' => 0.0,
                'severity' => null,
            ],
            [
                'number' => 'CT-20261118-4D2A90',
                'customer' => $customer,
                'carrier' => $carrierModels[0], // Maersk
                'origin' => $locModels[0], // Tanjung Priok
                'destination' => $locModels[2], // Singapore
                'status' => 'CREATED',
                'cargo' => 'Textiles & Garment Retail Stock',
                'weight' => 12000.00,
                'volume' => 36.00,
                'dep' => Carbon::now()->addDays(3),
                'arr' => Carbon::now()->addDays(5),
                'est' => Carbon::now()->addDays(5),
                'is_delayed' => false,
                'delay_hours' => 0.0,
                'severity' => null,
            ],
        ];

        foreach ($shipmentDefs as $def) {
            $shipment = Shipment::firstOrCreate(
                ['shipment_number' => $def['number']],
                [
                    'customer_id' => $def['customer']->id,
                    'carrier_id' => $def['carrier']->id,
                    'origin_id' => $def['origin']->id,
                    'destination_id' => $def['destination']->id,
                    'current_status' => $def['status'],
                    'cargo_type' => $def['cargo'],
                    'weight' => $def['weight'],
                    'volume' => $def['volume'],
                    'expected_departure' => $def['dep'],
                    'expected_arrival' => $def['arr'],
                    'estimated_arrival' => $def['est'],
                    'is_delayed' => $def['is_delayed'],
                    'delay_hours' => $def['delay_hours'],
                    'delay_severity' => $def['severity'],
                    'description' => "Standard commercial container delivery under contractual SLA.",
                ]
            );

            // Add Shipment Items
            ShipmentItem::firstOrCreate(
                ['shipment_id' => $shipment->id, 'item_name' => $def['cargo'] . ' Lot A'],
                ['quantity' => 150, 'weight' => $def['weight'] * 0.6, 'description' => 'Primary container cargo']
            );
            ShipmentItem::firstOrCreate(
                ['shipment_id' => $shipment->id, 'item_name' => $def['cargo'] . ' Lot B'],
                ['quantity' => 80, 'weight' => $def['weight'] * 0.4, 'description' => 'Secondary packaging']
            );

            // Add Tracking Events for in-transit / delivered shipments
            if (in_array($def['status'], ['IN_TRANSIT', 'DELIVERED', 'PICKED_UP'])) {
                // Event 1: Origin Departure
                TrackingEvent::firstOrCreate(
                    ['shipment_id' => $shipment->id, 'status_type' => 'PICKED_UP'],
                    [
                        'location_id' => $def['origin']->id,
                        'event_time' => Carbon::parse($def['dep'])->addHours(2),
                        'latitude' => $def['origin']->latitude,
                        'longitude' => $def['origin']->longitude,
                        'description' => "Container loaded onto vessel at {$def['origin']->name}.",
                        'created_by' => $operator->id,
                    ]
                );

                // Event 2: Waypoint / Maritime coordinate
                $midLat = ($def['origin']->latitude + $def['destination']->latitude) / 2;
                $midLng = ($def['origin']->longitude + $def['destination']->longitude) / 2;
                TrackingEvent::firstOrCreate(
                    ['shipment_id' => $shipment->id, 'status_type' => 'IN_TRANSIT'],
                    [
                        'location_id' => null,
                        'event_time' => Carbon::parse($def['dep'])->addDays(2),
                        'latitude' => $midLat,
                        'longitude' => $midLng,
                        'description' => "Vessel navigating international maritime corridor under steady propulsion.",
                        'created_by' => $operator->id,
                    ]
                );

                if ($def['status'] === 'DELIVERED') {
                    TrackingEvent::firstOrCreate(
                        ['shipment_id' => $shipment->id, 'status_type' => 'DELIVERED'],
                        [
                            'location_id' => $def['destination']->id,
                            'event_time' => $def['arr'],
                            'latitude' => $def['destination']->latitude,
                            'longitude' => $def['destination']->longitude,
                            'description' => "Cargo cleared customs and successfully signed off at final destination.",
                            'created_by' => $operator->id,
                        ]
                    );
                }
            }

            // Add Exceptions for delayed shipments
            if ($def['is_delayed']) {
                $type = $def['severity'] === 'HIGH' ? 'WEATHER_DELAY' : 'PORT_CONGESTION';
                ShipmentException::firstOrCreate(
                    ['shipment_id' => $shipment->id, 'exception_type' => $type],
                    [
                        'severity' => $def['severity'],
                        'status' => 'OPEN',
                        'notes' => "Severe operational bottleneck detected: {$def['delay_hours']} hours past baseline SLA.",
                        'reported_by' => $operator->id,
                    ]
                );
            }
        }

        // 5. Seed Audit Logs
        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'DATABASE_INITIALIZED',
            'auditable_type' => 'System',
            'auditable_id' => 1,
            'new_values' => ['status' => 'Seeded production data ready'],
            'ip_address' => '127.0.0.1',
        ]);
    }
}
