<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Region;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DriversSeeder extends Seeder
{
    public function run(): void
    {
        $regions = Region::query()->pluck('id')->all();
        $driverRoleId = Role::query()->where('name', 'driver')->value('id');
        $driverUsers = User::query()->where('role_id', $driverRoleId)->pluck('id')->all();

        for ($i = 1; $i <= 12; $i++) {
            Driver::query()->create([
                'user_id' => $driverUsers[$i - 1] ?? null,
                'region_id' => $regions ? $regions[array_rand($regions)] : null,
                'name' => fake()->name(),
                'phone' => fake()->phoneNumber(),
                'license_number' => 'SIM-' . strtoupper(Str::random(6)) . '-' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'status' => $i % 9 === 0 ? 'inactive' : 'active',
            ]);
        }
    }
}
