<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'center_id',
        'name',
        'type',
        'code',
        'status',
        'value',
        'notes',
    ];

    protected $casts = [
        'value' => 'decimal:2',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function maintenances()
    {
        return $this->hasMany(AssetMaintenance::class);
    }
}
