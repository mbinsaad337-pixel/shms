<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealSchedule extends Model
{
    protected $fillable = [
        'center_id',
        'meal_type',
        'start_time',
        'end_time',
        'late_deadline',
        'absent_deadline',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }
}
