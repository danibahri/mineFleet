<?php

namespace Database\Seeders;

use App\Models\VehicleType;
use Illuminate\Database\Seeder;

class VehicleTypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'angkutan_orang', 'description' => 'Kendaraan untuk transportasi orang'],
            ['name' => 'angkutan_barang', 'description' => 'Kendaraan untuk transportasi barang'],
        ];

        foreach ($types as $type) {
            VehicleType::query()->firstOrCreate(['name' => $type['name']], $type);
        }
    }
}
