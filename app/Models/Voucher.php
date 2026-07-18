<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
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
        return $this->belongsTo(Fund::class);
    }

    public function targetFund()
    {
        return $this->belongsTo(Fund::class, 'target_fund_id');
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
}
