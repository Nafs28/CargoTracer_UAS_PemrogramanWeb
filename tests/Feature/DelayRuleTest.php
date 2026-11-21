<?php

namespace Tests\Feature;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use App\Services\EtaService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DelayRuleTest extends TestCase
{
    use RefreshDatabase;

    protected EtaService $etaService;
    protected Shipment $shipment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->etaService = app(EtaService::class);

        $customer = User::factory()->create();
        $carrier = Carrier::create(['name' => 'CMA CGM', 'code' => 'CMDU']);
        $origin = Location::create(['name' => 'Hub 1', 'code' => 'H1', 'type' => 'port']);
        $dest = Location::create(['name' => 'Hub 2', 'code' => 'H2', 'type' => 'port']);

        $this->shipment = Shipment::create([
            'shipment_number' => 'CT-DELAY-TEST',
            'customer_id' => $customer->id,
            'carrier_id' => $carrier->id,
            'origin_id' => $origin->id,
            'destination_id' => $dest->id,
            'current_status' => 'IN_TRANSIT',
        ]);
    }

    public function test_no_delay_when_on_time(): void
    {
        $this->shipment->expected_arrival = Carbon::now()->addDays(5);
        $this->shipment->estimated_arrival = Carbon::now()->addDays(5);
        $this->shipment->save();

        $delayData = $this->etaService->calculateDelay($this->shipment);

        $this->assertFalse($delayData['is_delayed']);
        $this->assertEquals(0.0, $delayData['delay_hours']);
        $this->assertNull($delayData['severity']);
    }

    public function test_low_severity_delay_between_zero_and_six_hours(): void
    {
        $base = Carbon::now()->addDays(3);
        $this->shipment->expected_arrival = $base;
        $this->shipment->estimated_arrival = (clone $base)->addHours(4); // 4 hours late
        $this->shipment->save();

        $delayData = $this->etaService->calculateDelay($this->shipment);

        $this->assertTrue($delayData['is_delayed']);
        $this->assertEquals(4.0, $delayData['delay_hours']);
        $this->assertEquals('LOW', $delayData['severity']);
    }

    public function test_medium_severity_delay_between_six_and_twenty_four_hours(): void
    {
        $base = Carbon::now()->addDays(3);
        $this->shipment->expected_arrival = $base;
        $this->shipment->estimated_arrival = (clone $base)->addHours(18); // 18 hours late
        $this->shipment->save();

        $delayData = $this->etaService->calculateDelay($this->shipment);

        $this->assertTrue($delayData['is_delayed']);
        $this->assertEquals(18.0, $delayData['delay_hours']);
        $this->assertEquals('MEDIUM', $delayData['severity']);
    }

    public function test_high_severity_delay_greater_than_twenty_four_hours(): void
    {
        $base = Carbon::now()->addDays(3);
        $this->shipment->expected_arrival = $base;
        $this->shipment->estimated_arrival = (clone $base)->addHours(36); // 36 hours late
        $this->shipment->save();

        $delayData = $this->etaService->calculateDelay($this->shipment);

        $this->assertTrue($delayData['is_delayed']);
        $this->assertEquals(36.0, $delayData['delay_hours']);
        $this->assertEquals('HIGH', $delayData['severity']);
    }

    public function test_persisting_delay_engine_updates_shipment_columns(): void
    {
        $base = Carbon::now()->addDays(2);
        $this->shipment->expected_arrival = $base;
        $this->shipment->estimated_arrival = (clone $base)->addHours(30);
        $this->shipment->save();

        $this->etaService->updateShipmentDelay($this->shipment);

        $fresh = $this->shipment->fresh();
        $this->assertTrue($fresh->is_delayed);
        $this->assertEquals(30.0, $fresh->delay_hours);
        $this->assertEquals('HIGH', $fresh->delay_severity);
    }
}
