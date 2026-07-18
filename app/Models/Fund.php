<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fund extends Model
{
    protected $fillable = [
        'center_id',
        'name',
        'description',
        'balance',
        'is_system',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_system' => 'boolean',
    ];

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
