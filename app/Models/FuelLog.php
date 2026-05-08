<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'vehicle_id',
    'filled_by',
    'fuel_date',
    'liter',
    'price_per_liter',
    'total_cost',
    'odometer',
    'notes',
])]
class FuelLog extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'fuel_date' => 'date',
            'liter' => 'decimal:2',
            'price_per_liter' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'odometer' => 'integer',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function filledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'filled_by');
    }
}
