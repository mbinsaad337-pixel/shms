<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
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
