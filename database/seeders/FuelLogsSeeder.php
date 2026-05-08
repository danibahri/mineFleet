<?php

namespace Database\Seeders;

use App\Models\FuelLog;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class FuelLogsSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = Vehicle::query()->pluck('id')->all();
        $users = User::query()->pluck('id')->all();

        foreach ($vehicles as $vehicleId) {
            for ($i = 0; $i < 2; $i++) {
                $liter = rand(80, 220);
                $price = rand(13000, 16000);

                FuelLog::query()->create([
                    'vehicle_id' => $vehicleId,
                    'filled_by' => $users ? $users[array_rand($users)] : null,
                    'fuel_date' => now()->subDays(rand(1, 30))->toDateString(),
                    'liter' => $liter,
                    'price_per_liter' => $price,
                    'total_cost' => $liter * $price,
                    'odometer' => rand(25000, 150000),
                    'notes' => $i === 0 ? 'Routine refuel' : null,
                ]);
            }
        }
    }
}
