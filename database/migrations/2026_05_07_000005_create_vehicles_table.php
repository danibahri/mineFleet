<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('region_id')->constrained()->restrictOnDelete();
            $table->string('code')->unique();
            $table->string('plate_number')->unique();
            $table->string('brand');
            $table->string('model');
            $table->unsignedSmallInteger('year');
            $table->enum('ownership_type', ['company', 'rental']);
            $table->string('fuel_type', 50)->nullable();
            $table->decimal('fuel_consumption', 8, 2)->nullable();
            $table->unsignedBigInteger('odometer')->default(0);
            // Status used for availability checks and dashboard filters.
            $table->enum('status', ['available', 'booked', 'service', 'inactive'])->default('available');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['region_id', 'vehicle_type_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
