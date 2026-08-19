<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;

class Voucher extends Model
{
    use SoftDeletes;
    protected static function booted(): void
    {
        $ensureSettlementIsNotApproved = function (Voucher $voucher): void {
            if ($voucher->isLockedByApprovedSettlement()) {
                throw ValidationException::withMessages([
                    'voucher' => 'لا يمكن إنشاء أو تعديل أو حذف سند ضمن شهر تم اعتماد تصفيته المالية.',
                ]);
            }
        };

        static::creating($ensureSettlementIsNotApproved);
        static::updating($ensureSettlementIsNotApproved);
        static::deleting($ensureSettlementIsNotApproved);
    }

    protected $fillable = [
        'center_id',
        'voucher_number',
        'type',
        'sub_type',
        'fund_id',
        'target_fund_id',
        'student_id',
        'amount',
        'amount_text',
        'date',
        'payee_or_payer',
        'payment_method',
        'description',
        'status',
        'rejection_reason',
        'attachment',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function fund()
    {
        return $this->belongsTo(Fund::class)->withTrashed();
    }

    public function targetFund()
    {
        return $this->belongsTo(Fund::class, 'target_fund_id')->withTrashed();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function isLockedByApprovedSettlement(): bool
    {
        if (!$this->center_id || !$this->date) {
            return false;
        }

        return MonthlySettlement::where('center_id', $this->center_id)
            ->where('year', $this->date->year)
            ->where('month', $this->date->month)
            ->where('status', 'approved')
            ->exists();
    }
}
