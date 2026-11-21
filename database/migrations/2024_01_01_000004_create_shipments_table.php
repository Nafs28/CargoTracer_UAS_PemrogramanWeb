<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_number', 30)->unique();
            $table->foreignId('customer_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('carrier_id')->constrained('carriers')->restrictOnDelete();
            $table->foreignId('origin_id')->constrained('locations')->restrictOnDelete();
            $table->foreignId('destination_id')->constrained('locations')->restrictOnDelete();
            $table->enum('current_status', ['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED', 'CANCELLED'])->default('CREATED');
            $table->string('cargo_type')->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->decimal('volume', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->dateTime('expected_departure')->nullable();
            $table->dateTime('expected_arrival')->nullable();
            $table->dateTime('estimated_arrival')->nullable();
            $table->boolean('is_delayed')->default(false);
            $table->decimal('delay_hours', 8, 2)->nullable();
            $table->enum('delay_severity', ['LOW', 'MEDIUM', 'HIGH'])->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('current_status');
            $table->index('customer_id');
            $table->index('shipment_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
