<?php

namespace Tests\Feature;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatusTransitionTest extends TestCase
{
    use RefreshDatabase;

    protected User $operator;
    protected Shipment $shipment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->operator = User::factory()->create(['role' => 'operator']);
        $customer = User::factory()->create(['role' => 'customer']);
        $carrier = Carrier::create(['name' => 'Evergreen', 'code' => 'EGLV']);
        $origin = Location::create(['name' => 'Origin Port', 'code' => 'ORG', 'type' => 'port']);
        $dest = Location::create(['name' => 'Dest Port', 'code' => 'DST', 'type' => 'port']);

        $this->shipment = Shipment::create([
            'shipment_number' => 'CT-STATE-TEST',
            'customer_id' => $customer->id,
            'carrier_id' => $carrier->id,
            'origin_id' => $origin->id,
            'destination_id' => $dest->id,
            'current_status' => 'CREATED',
        ]);
    }

    public function test_valid_sequential_lifecycle_transitions(): void
    {
        // 1. CREATED -> BOOKED
        $res1 = $this->actingAs($this->operator, 'sanctum')
            ->patchJson("/api/shipments/{$this->shipment->id}/status", ['status' => 'BOOKED']);
        $res1->assertStatus(200);
        $this->assertEquals('BOOKED', $this->shipment->fresh()->current_status);

        // 2. BOOKED -> PICKED_UP
        $res2 = $this->actingAs($this->operator, 'sanctum')
            ->patchJson("/api/shipments/{$this->shipment->id}/status", ['status' => 'PICKED_UP']);
        $res2->assertStatus(200);
        $this->assertEquals('PICKED_UP', $this->shipment->fresh()->current_status);

        // 3. PICKED_UP -> IN_TRANSIT
        $res3 = $this->actingAs($this->operator, 'sanctum')
            ->patchJson("/api/shipments/{$this->shipment->id}/status", ['status' => 'IN_TRANSIT']);
        $res3->assertStatus(200);
        $this->assertEquals('IN_TRANSIT', $this->shipment->fresh()->current_status);

        // 4. IN_TRANSIT -> DELIVERED
        $res4 = $this->actingAs($this->operator, 'sanctum')
            ->patchJson("/api/shipments/{$this->shipment->id}/status", ['status' => 'DELIVERED']);
        $res4->assertStatus(200);
        $this->assertEquals('DELIVERED', $this->shipment->fresh()->current_status);
    }

    public function test_illegal_status_jump_from_created_to_delivered_is_rejected(): void
    {
        // Try skipping steps: CREATED -> DELIVERED directly
        $response = $this->actingAs($this->operator, 'sanctum')
            ->patchJson("/api/shipments/{$this->shipment->id}/status", ['status' => 'DELIVERED']);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertEquals('CREATED', $this->shipment->fresh()->current_status);
    }

    public function test_delivered_is_terminal_state_and_cannot_transition(): void
    {
        $this->shipment->current_status = 'DELIVERED';
        $this->shipment->save();

        $response = $this->actingAs($this->operator, 'sanctum')
            ->patchJson("/api/shipments/{$this->shipment->id}/status", ['status' => 'IN_TRANSIT']);

        $response->assertStatus(422);
        $this->assertEquals('DELIVERED', $this->shipment->fresh()->current_status);
    }

    public function test_cancelled_is_terminal_state(): void
    {
        $this->shipment->current_status = 'CANCELLED';
        $this->shipment->save();

        $response = $this->actingAs($this->operator, 'sanctum')
            ->patchJson("/api/shipments/{$this->shipment->id}/status", ['status' => 'BOOKED']);

        $response->assertStatus(422);
        $this->assertEquals('CANCELLED', $this->shipment->fresh()->current_status);
    }
}
