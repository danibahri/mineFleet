<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use App\Models\VehicleService;
use Illuminate\Database\Seeder;

class VehicleServicesSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = Vehicle::query()->pluck('id')->all();

        foreach ($vehicles as $vehicleId) {
            if (rand(1, 3) === 1) {
                VehicleService::query()->create([
                    'vehicle_id' => $vehicleId,
                    'service_date' => now()->subDays(rand(5, 45))->toDateString(),
                    'service_type' => fake()->randomElement(['Preventive', 'Corrective', 'Inspection']),
                    'workshop_name' => fake()->company(),
                    'cost' => rand(1500000, 6500000),
                    'odometer' => rand(30000, 160000),
                    'next_service_date' => now()->addDays(rand(30, 90))->toDateString(),
                    'notes' => rand(1, 3) === 1 ? 'Parts replaced and checked.' : null,
                ]);
            }
        }
    }
}
