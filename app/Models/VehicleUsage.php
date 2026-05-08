<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'booking_id',
    'vehicle_id',
    'driver_id',
    'start_odometer',
    'end_odometer',
    'actual_departure',
    'actual_return',
    'total_distance',
    'notes',
])]
class VehicleUsage extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'start_odometer' => 'integer',
            'end_odometer' => 'integer',
            'actual_departure' => 'datetime',
            'actual_return' => 'datetime',
            'total_distance' => 'integer',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(VehicleBooking::class, 'booking_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
