<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            RegionsSeeder::class,
            VehicleTypesSeeder::class,
            UsersSeeder::class,
            DriversSeeder::class,
            VehiclesSeeder::class,
            VehicleBookingsSeeder::class,
            BookingApprovalsSeeder::class,
            FuelLogsSeeder::class,
            VehicleServicesSeeder::class,
            VehicleUsagesSeeder::class,
            ActivityLogsSeeder::class,
        ]);
    }
}
