<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FoodBudget extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'center_id',
        'month',
        'year',
        'title',
        'total_amount',
        'days_count',
        'subscribers_count',
        'cost_per_student',
        'daily_rate',
        'last_payment_date',
        'status',
        'rejection_reason',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'last_payment_date' => 'date',
        'approved_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'cost_per_student' => 'decimal:2',
        'daily_rate' => 'decimal:2',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }
    public function lines()
    {
        return $this->hasMany(FoodBudgetLine::class, 'budget_id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public function subscriptions()
    {
        return $this->hasMany(FoodSubscription::class, 'budget_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'مسودة',
            'submitted' => 'بانتظار الاعتماد',
            'approved' => 'معتمدة',
            'rejected' => 'مرفوضة',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'submitted' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray',
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
}
