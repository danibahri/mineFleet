<?php

namespace Database\Seeders;

use App\Models\BookingApproval;
use App\Models\User;
use App\Models\VehicleBooking;
use Illuminate\Database\Seeder;

class BookingApprovalsSeeder extends Seeder
{
    public function run(): void
    {
        $approver1Id = User::query()->whereHas('role', fn($query) => $query->where('name', 'approver_level_1'))->value('id');
        $approver2Id = User::query()->whereHas('role', fn($query) => $query->where('name', 'approver_level_2'))->value('id');

        VehicleBooking::query()->each(function (VehicleBooking $booking) use ($approver1Id, $approver2Id): void {
            $level1Status = in_array($booking->status, ['pending', 'approved', 'completed', 'rejected'], true) ? 'approved' : 'pending';
            $level1ApprovedAt = $level1Status === 'approved' ? now()->subHours(rand(8, 72)) : null;

            BookingApproval::query()->create([
                'booking_id' => $booking->id,
                'approver_id' => $approver1Id,
                'approval_level' => 1,
                'status' => $booking->status === 'pending' ? 'pending' : $level1Status,
                'notes' => $booking->status === 'rejected' ? 'Initial review approved.' : null,
                'approved_at' => $level1ApprovedAt,
            ]);

            if (in_array($booking->status, ['approved', 'completed', 'rejected'], true)) {
                BookingApproval::query()->create([
                    'booking_id' => $booking->id,
                    'approver_id' => $approver2Id,
                    'approval_level' => 2,
                    'status' => $booking->status === 'rejected' ? 'rejected' : 'approved',
                    'notes' => $booking->status === 'rejected' ? 'Dispatch conflict detected.' : null,
                    'approved_at' => now()->subHours(rand(2, 24)),
                ]);
            }
        });
    }
}
