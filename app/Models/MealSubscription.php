<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealSubscription extends Model
{
    protected $fillable = [
        'student_id',
        'center_id',
        'type',
        'meal_types',
        'start_date',
        'end_date',
        'amount',
        'status',
        'suspended_reason',
        'created_by',
    ];

    protected $casts = [
        'meal_types' => 'json',
        'start_date' => 'date',
        'end_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function distributions()
    {
        return $this->hasMany(MealDistribution::class, 'subscription_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
