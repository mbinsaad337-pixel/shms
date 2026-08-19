<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonthlySettlement extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'center_id',
        'month',
        'year',
        'total_budget',
        'total_spent',
        'total_remaining',
        'status',
        'submitted_by',
        'submitted_at',
        'approved_by',
        'approved_at',
        'notes',
        'rejection_reason',
    ];

    protected $casts = [
        'total_budget' => 'decimal:2',
        'total_spent' => 'decimal:2',
        'total_remaining' => 'decimal:2',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function details()
    {
        return $this->hasMany(SettlementDetail::class, 'settlement_id');
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
