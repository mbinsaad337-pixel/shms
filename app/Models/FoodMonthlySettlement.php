<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FoodMonthlySettlement extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'center_id',
        'budget_id',
        'month',
        'year',
        'total_revenue',
        'total_expenses',
        'total_debt',
        'net_result',
        'result_type',
        'status',
        'notes',
        'rejection_reason',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'total_revenue' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'total_debt' => 'decimal:2',
        'net_result' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }
    public function budget()
    {
        return $this->belongsTo(FoodBudget::class, 'budget_id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'draft' => 'مسودة',
            'submitted' => 'بانتظار الاعتماد',
            'approved' => 'معتمدة',
            'rejected' => 'مرفوضة',
            default => $this->status,
        };
    }

    public function getResultTypeLabel(): string
    {
        return match ($this->result_type) {
            'surplus' => 'فائض',
            'deficit' => 'عجز',
            'break_even' => 'تعادل',
            default => $this->result_type,
        };
    }

    public function getMonthNameAttribute(): string
    {
        $months = [
            1 => 'يناير',
            2 => 'فبراير',
            3 => 'مارس',
            4 => 'أبريل',
            5 => 'مايو',
            6 => 'يونيو',
            7 => 'يوليو',
            8 => 'أغسطس',
            9 => 'سبتمبر',
            10 => 'أكتوبر',
            11 => 'نوفمبر',
            12 => 'ديسمبر',
        ];
        return $months[$this->month] ?? $this->month;
    }
public function details()
{
    return $this->hasMany(FoodMonthlySettlementDetail::class, 'settlement_id');
}
}
