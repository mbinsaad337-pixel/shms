<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleViolation extends Model
{
    use SoftDeletes;
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
