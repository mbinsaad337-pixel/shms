<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CenterExpense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'center_id',
        'type',
        'amount',
        'due_date',
        'payment_date',
        'status',
        'receipt',
        'receipt_type',
        'month',
        'year',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function getReceiptUrlAttribute()
    {
        return $this->receipt ? asset('storage/' . $this->receipt) : null;
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'rent'        => 'إيجار السكن',
            'water'       => 'فاتورة الماء',
            'electricity' => 'فاتورة الكهرباء',
            'internet'    => 'فاتورة الانترنت',
            'other'       => 'مصروفات اخرى',
            default       => $this->type,
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'rent'        => 'fa-building',
            'water'       => 'fa-tint',
            'electricity' => 'fa-bolt',
            default       => 'fa-money-bill',
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'rent'        => 'text-purple-600 bg-purple-100',
            'water'       => 'text-blue-600 bg-blue-100',
            'electricity' => 'text-yellow-600 bg-yellow-100',
            default       => 'text-gray-600 bg-gray-100',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'غير مدفوع (مستحق)',
            'paid'    => 'تم الدفع',
            default   => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-red-100 text-red-700',
            'paid'    => 'bg-green-100 text-green-700',
            default   => 'bg-gray-100 text-gray-700',
        };
    }
}
