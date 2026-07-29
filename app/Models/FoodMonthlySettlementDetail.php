<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FoodMonthlySettlementDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'settlement_id',
        'fund_id',
        'opening_balance',
        'total_income',
        'total_expense',
        'closing_balance',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'total_income' => 'decimal:2',
        'total_expense' => 'decimal:2',
        'closing_balance' => 'decimal:2',
    ];

    public function settlement()
    {
        return $this->belongsTo(FoodMonthlySettlement::class, 'settlement_id');
    }

    public function fund()
    {
        return $this->belongsTo(Fund::class, 'fund_id');
    }
}
