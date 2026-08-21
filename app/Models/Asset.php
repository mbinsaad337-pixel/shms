<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'center_id',
        'name',
        'type',
        'category',
        'code',
        'status',
        'value',
        'currency',
        'notes',
        'photo',
    ];

    protected $casts = [
        'value' => 'decimal:2',
    ];

    public function getCurrencyLabelAttribute(): string
    {
        return \App\Support\Currency::label($this->currency ?? null);
    }

    public function getCurrencySymbolAttribute(): string
    {
        return \App\Support\Currency::symbol($this->currency ?? null);
    }

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function maintenances()
    {
        return $this->hasMany(AssetMaintenance::class);
    }
}
