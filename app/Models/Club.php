<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Club extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'center_id',
        'name',
        'description',
        'category',
        'logo',
        'status',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function members()
    {
        return $this->hasMany(ClubMember::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
}
