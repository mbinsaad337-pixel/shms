<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetItem extends Model
{
    protected $fillable = [
        'monthly_budget_id',
        'fund_id',
        'requested_amount',
        'approved_amount',
        'justification',
        'notes',
        'attachment_pdf',
    ];

    protected $casts = [
        'requested_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
    ];

    public function monthlyBudget()
    {
        return $this->belongsTo(MonthlyBudget::class);
    }

    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }
}
