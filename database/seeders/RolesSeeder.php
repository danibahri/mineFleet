<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'description' => 'System administrator'],
            ['name' => 'approver_level_1', 'description' => 'First level approval'],
            ['name' => 'approver_level_2', 'description' => 'Second level approval'],
            ['name' => 'driver', 'description' => 'Operational driver'],
        ];

        foreach ($roles as $role) {
            Role::query()->firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
