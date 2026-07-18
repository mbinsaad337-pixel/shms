<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Center extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'logo',
        'is_active',
    ];

    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/' . $this->logo) : asset('images/logos/alawayil_logo.png');
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function funds()
    {
        return $this->hasMany(Fund::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }
}
