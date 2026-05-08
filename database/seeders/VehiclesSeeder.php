<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VehiclesSeeder extends Seeder
{
    public function run(): void
    {
        $types = VehicleType::query()->pluck('id', 'name');
        $regions = Region::query()->pluck('id')->all();

        $vehicleProfiles = [
            ['brand' => 'Toyota', 'model' => 'Hilux 4x4', 'fuel' => 'Diesel'],
            ['brand' => 'Mitsubishi', 'model' => 'Triton', 'fuel' => 'Diesel'],
            ['brand' => 'Isuzu', 'model' => 'Service Truck', 'fuel' => 'Diesel'],
            ['brand' => 'Hino', 'model' => 'Bus 32 Seat', 'fuel' => 'Diesel'],
            ['brand' => 'Nissan', 'model' => 'Patrol', 'fuel' => 'Diesel'],
        ];

        for ($i = 1; $i <= 18; $i++) {
            $profile = $vehicleProfiles[array_rand($vehicleProfiles)];
            $typeName = $i % 3 === 0 ? 'angkutan_barang' : 'angkutan_orang';

            Vehicle::query()->create([
                'vehicle_type_id' => $types[$typeName] ?? null,
                'region_id' => $regions ? $regions[array_rand($regions)] : null,
                'code' => 'VH-' . strtoupper(Str::random(3)) . '-' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'plate_number' => 'B ' . rand(1000, 9999) . ' ' . strtoupper(Str::random(3)),
                'brand' => $profile['brand'],
                'model' => $profile['model'],
                'year' => rand(2018, 2024),
                'ownership_type' => $i % 5 === 0 ? 'rental' : 'company',
                'fuel_type' => $profile['fuel'],
                'fuel_consumption' => rand(6, 12) + (rand(0, 9) / 10),
                'odometer' => rand(25000, 150000),
                'status' => $i % 7 === 0 ? 'service' : ($i % 5 === 0 ? 'inactive' : 'available'),
                'notes' => $i % 4 === 0 ? 'Scheduled for next inspection.' : null,
            ]);
        }
    }
}
