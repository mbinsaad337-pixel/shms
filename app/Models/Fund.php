<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fund extends Model
{
    use SoftDeletes;
    public const CURRENCIES = [
        'YER' => 'ريال يمني',
        'SAR' => 'ريال سعودي',
        'USD' => 'دولار أمريكي',
    ];

    public const CURRENCY_SYMBOLS = [
        'YER' => 'ر.ي',
        'SAR' => 'ر.س',
        'USD' => '$',
    ];

    protected $fillable = [
        'center_id',
        'name',
        'description',
        'balance',
        'currency',
        'is_system',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_system' => 'boolean',
    ];

    public function getCurrencyLabelAttribute(): string
    {
        return self::CURRENCIES[$this->currency] ?? self::CURRENCIES['YER'];
    }

    public function getCurrencySymbolAttribute(): string
    {
        return self::CURRENCY_SYMBOLS[$this->currency] ?? self::CURRENCY_SYMBOLS['YER'];
    }

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    public function budgetItems()
    {
        return $this->hasMany(BudgetItem::class);
    }
}
