<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FoodPurchaseInvoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'center_id',
        'supplier_id',
        'invoice_number',
        'invoice_date',
        'total_amount',
        'payment_type',
        'status',
        'attachment',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }
    public function supplier()
    {
        return $this->belongsTo(FoodSupplier::class, 'supplier_id');
    }
    public function items()
    {
        return $this->hasMany(FoodInvoiceItem::class, 'invoice_id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getPaymentTypeLabelAttribute(): string
    {
        return $this->payment_type === 'cash' ? 'نقدي' : 'آجل';
    }

    protected static function booted(): void
    {
        static::created(function (FoodPurchaseInvoice $invoice) {
            if ($invoice->status === 'approved') {
                if ($invoice->payment_type === 'cash') {
                    $invoice->createAutoVoucher();
                }
                $invoice->supplier->recalculateBalance();
            }
        });
        static::updated(function (FoodPurchaseInvoice $invoice) {
            if ($invoice->isDirty('status') && $invoice->status === 'approved' && $invoice->payment_type === 'cash') {
                $invoice->createAutoVoucher();
            }
            $invoice->supplier->recalculateBalance();
        });
    }

    public function createAutoVoucher(): void
    {
        $description = "سداد تلقائي للفاتورة رقم: {$this->invoice_number}";

        $exists = FoodVoucher::where('supplier_id', $this->supplier_id)
            ->where('amount', $this->total_amount)
            ->where('description', $description)
            ->exists();

        if (!$exists) {
            $nextNumber = 'FV-' . date('Ym') . '-' . str_pad(
                FoodVoucher::where('center_id', $this->center_id)->count() + 1,
                3,
                '0',
                STR_PAD_LEFT
            );

            FoodVoucher::create([
                'center_id' => $this->center_id,
                'voucher_number' => $nextNumber,
                'type' => 'payment',
                'voucher_date' => $this->invoice_date,
                'supplier_id' => $this->supplier_id,
                'amount' => $this->total_amount,
                'description' => $description,
                'status' => 'active',
                'created_by' => $this->created_by,
            ]);
        }
    }
}
