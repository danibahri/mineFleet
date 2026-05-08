<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'booking_code',
    'requester_id',
    'vehicle_id',
    'driver_id',
    'created_by',
    'current_approval_level',
    'current_approver_id',
    'purpose',
    'destination',
    'departure_date',
    'return_date',
    'status',
    'rejection_reason',
    'approved_at',
    'completed_at',
])]
class VehicleBooking extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'current_approval_level' => 'integer',
            'departure_date' => 'datetime',
            'return_date' => 'datetime',
            'approved_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function currentApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_approver_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(BookingApproval::class, 'booking_id');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(VehicleUsage::class, 'booking_id');
    }
}
