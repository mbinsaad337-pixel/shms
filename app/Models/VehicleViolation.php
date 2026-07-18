<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleViolation extends Model
{
    protected $fillable = [
        'vehicle_id',
        'violation_date',
        'description',
        'fine_amount',
        'status',
    ];

    protected $casts = [
        'violation_date' => 'date',
        'fine_amount' => 'decimal:2',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
