<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealGroupMember extends Model
{
    protected $fillable = [
        'group_id',
        'student_id',
    ];

    public function group()
    {
        return $this->belongsTo(MealGroup::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
