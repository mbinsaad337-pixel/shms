<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodBudgetLine extends Model
{
    protected $fillable = [
        'budget_id',
        'item_name',
        'days',
        'quantity',
        'unit_price',
        'total',
        'supplier_name',
        'sort_order',
    ];

    protected $casts = [
        'days' => 'integer',
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function budget()
    {
        return $this->belongsTo(FoodBudget::class, 'budget_id');
    }

    public function calculateTotal(): float
    {
        $days = $this->days ?? 1;
        $qty = $this->quantity ?? 1;
        $price = $this->unit_price ?? 0;
        return $days * $qty * $price;
    }
}
