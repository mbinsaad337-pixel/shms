<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FoodVoucher extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'center_id',
        'voucher_number',
        'type',
        'voucher_date',
        'supplier_id',
        'student_id',
        'amount',
        'description',
        'attachment',
        'status',
        'created_by',
    ];

    protected $casts = [
        'voucher_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }
    public function supplier()
    {
        return $this->belongsTo(FoodSupplier::class, 'supplier_id');
    }
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeLabel(): string
    {
        return $this->type === 'payment' ? 'سند صرف' : 'سند قبض';
    }

    protected static function booted(): void
    {
        static::created(function (FoodVoucher $voucher) {
            if ($voucher->supplier_id) {
                $voucher->supplier->recalculateBalance();
            }
        });
        static::updated(function (FoodVoucher $voucher) {
            if ($voucher->supplier_id) {
                $voucher->supplier->recalculateBalance();
            }
        });
        static::deleted(function (FoodVoucher $voucher) {
            if ($voucher->supplier_id) {
                $voucher->supplier->recalculateBalance();
            }
        });
    }
}
