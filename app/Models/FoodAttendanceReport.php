<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodAttendanceReport extends Model
{
    protected $fillable = [
        'student_id',
        'meal_date',
        'meal_type',
        'status',
    ];

    protected $casts = [
        'meal_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
