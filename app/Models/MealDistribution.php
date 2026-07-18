<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealDistribution extends Model
{
    protected $fillable = [
        'student_id',
        'subscription_id',
        'meal_type',
        'distributed_at',
        'distributed_by',
        'group_id',
        'plate_barcode',
    ];

    protected $casts = [
        'distributed_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subscription()
    {
        return $this->belongsTo(MealSubscription::class, 'subscription_id');
    }

    public function distributor()
    {
        return $this->belongsTo(User::class, 'distributed_by');
    }

    public function group()
    {
        return $this->belongsTo(MealGroup::class, 'group_id');
    }
}
