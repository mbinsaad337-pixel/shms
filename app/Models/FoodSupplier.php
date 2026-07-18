<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FoodSupplier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'center_id',
        'name',
        'address',
        'phone',
        'email',
        'tax_number',
        'balance_debit',
        'balance_credit',
        'notes',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'balance_debit' => 'decimal:2',
        'balance_credit' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function invoices()
    {
        return $this->hasMany(FoodPurchaseInvoice::class, 'supplier_id');
    }
    public function vouchers()
    {
        return $this->hasMany(FoodVoucher::class, 'supplier_id');
    }

    public function getNetBalanceAttribute(): float
    {
        return $this->balance_debit - $this->balance_credit;
    }

    public function recalculateBalance(): void
    {
        // مدين: إجمالي الفواتير
        $this->balance_debit = $this->invoices()->where('status', 'approved')->sum('total_amount');
        // دائن: سندات الصرف
        $this->balance_credit = $this->vouchers()->where('type', 'payment')->where('status', 'active')->sum('amount');
        $this->save();
    }
}
