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
        'has_rent',
        'leave_cutoff_time',
        'message',
        'vision',
        'values',
        'goals',
        'whatsapp_link',
        'instagram_link',
        'facebook_link',
        'location_link',
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

    /**
     * كل طاقم المركز، مع استثناء حسابات الطلاب ووظائف الوحدات المستقلة.
     */
    public function staff()
    {
        return $this->users()->doesntHave('student')->whereDoesntHave('roles', function ($query) {
            $query->whereIn('name', [
                'student',
                'nutrition-manager',
                'circle-teacher',
                'transport-manager',
            ]);
        });
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function residents()
    {
        return $this->students()->whereHas('activeRoomAssignment');
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

    public function expenses()
    {
        return $this->hasMany(CenterExpense::class);
    }
}
