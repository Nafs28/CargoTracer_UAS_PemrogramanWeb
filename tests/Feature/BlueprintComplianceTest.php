<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use App\Services\EtaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BlueprintComplianceTest extends TestCase
{
    use RefreshDatabase;

    private function createMasterData(): array
    {
        $carrier = Carrier::create(['name' => 'Maersk Line', 'code' => 'MAEU']);
        $origin = Location::create(['name' => 'Tanjung Priok', 'code' => 'IDJKT', 'latitude' => -6.10, 'longitude' => 106.88]);
        $dest = Location::create(['name' => 'Singapore', 'code' => 'SGSIN', 'latitude' => 1.26, 'longitude' => 103.82]);

        return [$carrier, $origin, $dest];
    }

    public function test_configurable_delay_thresholds_from_config_file(): void
    {
        $etaService = app(EtaService::class);

        // Default config: low <= 6, medium <= 24
        $this->assertEquals('MEDIUM', $etaService->determineSeverity(10.0));

        // Simulate Live Coding Challenge: change threshold low to 12 hours
        config(['cargotrace.delay_thresholds.low' => 12.0]);
        $this->assertEquals('LOW', $etaService->determineSeverity(10.0));
    }

    public function test_admin_can_override_terminal_status_and_logs_audit(): void
    {
        [$carrier, $origin, $dest] = $this->createMasterData();
        $admin = User::factory()->create(['role' => 'admin']);
        $operator = User::factory()->create(['role' => 'operator']);
        $customer = User::factory()->create(['role' => 'customer']);

        $shipment = Shipment::create([
            'shipment_number' => 'CT-TEST-OVR1',
            'customer_id' => $customer->id,
            'carrier_id' => $carrier->id,
            'origin_id' => $origin->id,
            'destination_id' => $dest->id,
            'current_status' => 'DELIVERED',
        ]);

        // Operator cannot override
        Sanctum::actingAs($operator);
        $this->postJson("/api/shipments/{$shipment->id}/override-status", [
            'status' => 'IN_TRANSIT',
            'reason' => 'Correction test',
        ])->assertStatus(403);

        // Admin can override and audit log is recorded
        Sanctum::actingAs($admin);
        $this->postJson("/api/shipments/{$shipment->id}/override-status", [
            'status' => 'IN_TRANSIT',
            'reason' => 'Customs re-inspection required',
        ])->assertStatus(200)
          ->assertJsonPath('data.current_status', 'IN_TRANSIT');

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'ADMIN_OVERRIDE',
            'auditable_id' => $shipment->id,
        ]);
    }

    public function test_notifications_created_on_status_transition_and_can_be_marked_read(): void
    {
        [$carrier, $origin, $dest] = $this->createMasterData();
        $operator = User::factory()->create(['role' => 'operator']);
        $customer = User::factory()->create(['role' => 'customer']);

        $shipment = Shipment::create([
            'shipment_number' => 'CT-TEST-NOTIF1',
            'customer_id' => $customer->id,
            'carrier_id' => $carrier->id,
            'origin_id' => $origin->id,
            'destination_id' => $dest->id,
            'current_status' => 'CREATED',
        ]);

        Sanctum::actingAs($operator);
        $this->patchJson("/api/shipments/{$shipment->id}/status", ['status' => 'BOOKED'])
            ->assertStatus(200);

        Sanctum::actingAs($customer);
        $res = $this->getJson('/api/notifications')->assertStatus(200);
        $this->assertEquals(1, $res->json('unread_count'));

        $this->patchJson('/api/notifications/read-all')->assertStatus(200);
        $this->getJson('/api/notifications')->assertJsonPath('unread_count', 0);
    }

    public function test_admin_user_management_crud_and_rbac(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $operator = User::factory()->create(['role' => 'operator']);

        Sanctum::actingAs($operator);
        $this->getJson('/api/users')->assertStatus(403);

        Sanctum::actingAs($admin);
        $this->postJson('/api/users', [
            'name' => 'New Operator',
            'email' => 'newop@cargotrace.com',
            'password' => 'secret123',
            'role' => 'operator',
        ])->assertStatus(201);

        $this->assertDatabaseHas('users', ['email' => 'newop@cargotrace.com', 'role' => 'operator']);
    }
}
