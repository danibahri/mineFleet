<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionsSeeder extends Seeder
{
    public function run(): void
    {
        $regions = [
            ['name' => 'Pit North', 'code' => 'PN', 'description' => 'Northern pit operations'],
            ['name' => 'Pit South', 'code' => 'PS', 'description' => 'Southern pit operations'],
            ['name' => 'Hauling Road', 'code' => 'HR', 'description' => 'Main hauling corridor'],
            ['name' => 'Workshop', 'code' => 'WS', 'description' => 'Maintenance and service area'],
            ['name' => 'Camp Site', 'code' => 'CS', 'description' => 'Base camp and logistics'],
        ];

        foreach ($regions as $region) {
            Region::query()->firstOrCreate(['code' => $region['code']], $region);
        }
    }
}
