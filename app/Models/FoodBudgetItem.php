<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodBudgetItem extends Model
{
    protected $fillable = [
        'center_id',
        'month',
        'year',
        'item_name',
        'unit',
        'quantity',
        'unit_price',
        'total_price',
        'fund_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }
}
