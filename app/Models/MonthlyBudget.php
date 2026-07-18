<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyBudget extends Model
{
    protected $fillable = [
        'center_id',
        'month',
        'year',
        'total_amount',
        'status',
        'submitted_by',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function items()
    {
        return $this->hasMany(BudgetItem::class);
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
