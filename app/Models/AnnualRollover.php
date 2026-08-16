<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnualRollover extends Model
{
    protected $fillable = [
        'center_id',
        'year',
        'from_date',
        'to_date',
        'performed_by',
        'modules',
        'summary',
        'notes',
    ];

    protected $casts = [
        'modules' => 'array',
        'summary' => 'array',
        'from_date' => 'date',
        'to_date' => 'date',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function archives()
    {
        return $this->hasMany(AnnualArchive::class, 'rollover_id');
    }
}
