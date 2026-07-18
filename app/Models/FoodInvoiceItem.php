<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodInvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'item_name',
        'quantity',
        'unit',
        'unit_price',
        'total',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(FoodPurchaseInvoice::class, 'invoice_id');
    }
}
