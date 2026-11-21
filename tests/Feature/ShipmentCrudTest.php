<?php

namespace Tests\Feature;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShipmentCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $operator;
    protected User $customer1;
    protected User $customer2;
    protected Carrier $carrier;
    protected Location $origin;
    protected Location $destination;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->operator = User::factory()->create(['role' => 'operator']);
        $this->customer1 = User::factory()->create(['role' => 'customer']);
        $this->customer2 = User::factory()->create(['role' => 'customer']);

        $this->carrier = Carrier::create([
            'name' => 'Maersk Line',
            'code' => 'MAEU',
            'is_active' => true,
        ]);

        $this->origin = Location::create([
            'name' => 'Jakarta Hub',
            'code' => 'JKTHUB',
            'type' => 'port',
            'latitude' => -6.1,
            'longitude' => 106.8,
        ]);

        $this->destination = Location::create([
            'name' => 'Rotterdam Port',
            'code' => 'RTMHUB',
            'type' => 'port',
            'latitude' => 51.9,
            'longitude' => 4.4,
        ]);
    }

    public function test_admin_and_operator_can_create_shipment(): void
    {
        $payload = [
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'cargo_type' => 'Semiconductors',
            'weight' => 5400.50,
            'volume' => 20.0,
            'expected_departure' => now()->toISOString(),
            'expected_arrival' => now()->addDays(10)->toISOString(),
        ];

        $response = $this->actingAs($this->operator, 'sanctum')->postJson('/api/shipments', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('shipments', [
            'cargo_type' => 'Semiconductors',
            'current_status' => 'CREATED',
        ]);
    }

    public function test_customer_isolation_customer_only_sees_own_shipments(): void
    {
        // Shipment for customer 1
        $shipment1 = Shipment::create([
            'shipment_number' => 'CT-TEST-001',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'current_status' => 'CREATED',
        ]);

        // Shipment for customer 2
        $shipment2 = Shipment::create([
            'shipment_number' => 'CT-TEST-002',
            'customer_id' => $this->customer2->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'current_status' => 'CREATED',
        ]);

        // Customer 1 queries list
        $response1 = $this->actingAs($this->customer1, 'sanctum')->getJson('/api/shipments');
        $response1->assertStatus(200);
        $data1 = $response1->json('data');

        $this->assertCount(1, $data1);
        $this->assertEquals('CT-TEST-001', $data1[0]['shipment_number']);

        // Customer 1 cannot access customer 2 detail
        $forbiddenResponse = $this->actingAs($this->customer1, 'sanctum')->getJson("/api/shipments/{$shipment2->id}");
        $forbiddenResponse->assertStatus(403);
    }

    public function test_admin_can_view_all_shipments(): void
    {
        Shipment::create([
            'shipment_number' => 'CT-TEST-001',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'current_status' => 'CREATED',
        ]);

        Shipment::create([
            'shipment_number' => 'CT-TEST-002',
            'customer_id' => $this->customer2->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'current_status' => 'CREATED',
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')->getJson('/api/shipments');
        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_only_admin_can_delete_shipment(): void
    {
        $shipment = Shipment::create([
            'shipment_number' => 'CT-TEST-DEL',
            'customer_id' => $this->customer1->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'current_status' => 'CREATED',
        ]);

        // Customer delete forbidden
        $resCust = $this->actingAs($this->customer1, 'sanctum')->deleteJson("/api/shipments/{$shipment->id}");
        $resCust->assertStatus(403);

        // Operator delete forbidden
        $resOp = $this->actingAs($this->operator, 'sanctum')->deleteJson("/api/shipments/{$shipment->id}");
        $resOp->assertStatus(403);

        // Admin delete permitted (soft delete)
        $resAdmin = $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/shipments/{$shipment->id}");
        $resAdmin->assertStatus(200);

        $this->assertSoftDeleted('shipments', ['id' => $shipment->id]);
    }
}
