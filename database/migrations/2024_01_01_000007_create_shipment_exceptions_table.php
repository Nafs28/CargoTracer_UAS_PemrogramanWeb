<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->enum('exception_type', ['CUSTOMS_HOLD', 'WEATHER_DELAY', 'PORT_CONGESTION', 'DAMAGED', 'MISROUTED', 'DOCUMENTATION_ISSUE']);
            $table->enum('severity', ['LOW', 'MEDIUM', 'HIGH'])->default('LOW');
            $table->enum('status', ['OPEN', 'INVESTIGATING', 'RESOLVED'])->default('OPEN');
            $table->text('notes')->nullable();
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('resolved_at')->nullable();
            $table->timestamps();

            $table->index('shipment_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_exceptions');
    }
};
