<?php

namespace Database\Seeders;

use App\Models\VehicleBooking;
use App\Models\VehicleUsage;
use Illuminate\Database\Seeder;

class VehicleUsagesSeeder extends Seeder
{
    public function run(): void
    {
        $completedBookings = VehicleBooking::query()->where('status', 'completed')->get();

        foreach ($completedBookings as $booking) {
            $startOdometer = rand(30000, 160000);
            $distance = rand(40, 220);

            VehicleUsage::query()->create([
                'booking_id' => $booking->id,
                'vehicle_id' => $booking->vehicle_id,
                'driver_id' => $booking->driver_id,
                'start_odometer' => $startOdometer,
                'end_odometer' => $startOdometer + $distance,
                'actual_departure' => $booking->departure_date,
                'actual_return' => $booking->return_date,
                'total_distance' => $distance,
                'notes' => rand(1, 3) === 1 ? 'Trip completed without incident.' : null,
            ]);
        }
    }
}
