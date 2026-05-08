<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleBooking;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VehicleBookingsSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = Vehicle::query()->pluck('id')->all();
        $drivers = Driver::query()->pluck('id')->all();
        $requesters = User::query()->pluck('id')->all();

        $approver1Id = User::query()->whereHas('role', fn($query) => $query->where('name', 'approver_level_1'))->value('id');
        $approver2Id = User::query()->whereHas('role', fn($query) => $query->where('name', 'approver_level_2'))->value('id');
        $creatorId = User::query()->whereHas('role', fn($query) => $query->where('name', 'admin'))->value('id');

        for ($i = 1; $i <= 12; $i++) {
            $departure = now()->addDays(rand(-2, 7))->setTime(rand(6, 12), 0);
            $returnDate = (clone $departure)->addHours(rand(4, 10));
            $statusPool = ['pending', 'approved', 'completed', 'rejected', 'cancelled'];
            $status = $statusPool[array_rand($statusPool)];

            $currentApprovalLevel = $status === 'pending' ? 1 : 2;
            $currentApproverId = $status === 'pending' ? $approver1Id : $approver2Id;
            $approvedAt = in_array($status, ['approved', 'completed'], true) ? now()->subHours(rand(6, 48)) : null;
            $completedAt = $status === 'completed' ? now()->subHours(rand(1, 24)) : null;
            $rejectionReason = $status === 'rejected' ? 'Conflict with emergency dispatch.' : null;

            VehicleBooking::query()->create([
                'booking_code' => 'MF-' . now()->format('ymd') . '-' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'requester_id' => $requesters ? $requesters[array_rand($requesters)] : null,
                'vehicle_id' => $vehicles ? $vehicles[array_rand($vehicles)] : null,
                'driver_id' => $drivers ? $drivers[array_rand($drivers)] : null,
                'created_by' => $creatorId,
                'current_approval_level' => $currentApprovalLevel,
                'current_approver_id' => $currentApproverId,
                'purpose' => 'Operational support for ' . Str::title(fake()->word()) . ' area.',
                'destination' => fake()->randomElement(['Pit North', 'Pit South', 'Workshop', 'Hauling Road', 'Camp Site']),
                'departure_date' => $departure,
                'return_date' => $returnDate,
                'status' => $status,
                'rejection_reason' => $rejectionReason,
                'approved_at' => $approvedAt,
                'completed_at' => $completedAt,
            ]);
        }
    }
}
