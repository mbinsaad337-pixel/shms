<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettlementDetail extends Model
{
    protected $fillable = [
        'settlement_id',
        'fund_id',
        'opening_balance',
        'total_income',
        'total_expense',
        'transfers_in',
        'transfers_out',
        'closing_balance',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'total_income' => 'decimal:2',
        'total_expense' => 'decimal:2',
        'transfers_in' => 'decimal:2',
        'transfers_out' => 'decimal:2',
        'closing_balance' => 'decimal:2',
    ];

    public function settlement()
    {
        return $this->belongsTo(MonthlySettlement::class, 'settlement_id');
    }

    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }
}
