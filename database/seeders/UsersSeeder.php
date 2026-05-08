<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::query()->pluck('id', 'name');
        $regions = Region::query()->pluck('id')->all();

        User::query()->firstOrCreate(
            ['email' => 'admin@minefleet.test'],
            [
                'name' => 'MineFleet Admin',
                'password' => Hash::make('password'),
                'role_id' => $roles['admin'] ?? null,
                'region_id' => $regions[0] ?? null,
                'phone' => '0812-0000-0001',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        User::query()->firstOrCreate(
            ['email' => 'approver1@minefleet.test'],
            [
                'name' => 'Approver Level 1',
                'password' => Hash::make('password'),
                'role_id' => $roles['approver_level_1'] ?? null,
                'region_id' => $regions[1] ?? null,
                'phone' => '0812-0000-0002',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        User::query()->firstOrCreate(
            ['email' => 'approver2@minefleet.test'],
            [
                'name' => 'Approver Level 2',
                'password' => Hash::make('password'),
                'role_id' => $roles['approver_level_2'] ?? null,
                'region_id' => $regions[2] ?? null,
                'phone' => '0812-0000-0003',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        User::query()->firstOrCreate(
            ['email' => 'driver.lead@minefleet.test'],
            [
                'name' => 'Driver Lead',
                'password' => Hash::make('password'),
                'role_id' => $roles['driver'] ?? null,
                'region_id' => $regions[3] ?? null,
                'phone' => '0812-0000-0004',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        User::factory()
            ->count(12)
            ->state(function () use ($roles, $regions) {
                $roleKeys = ['admin', 'approver_level_1', 'approver_level_2', 'driver'];

                return [
                    'role_id' => $roles[$roleKeys[array_rand($roleKeys)]] ?? null,
                    'region_id' => $regions ? $regions[array_rand($regions)] : null,
                    'phone' => fake()->phoneNumber(),
                    'status' => 'active',
                ];
            })
            ->create();
    }
}
