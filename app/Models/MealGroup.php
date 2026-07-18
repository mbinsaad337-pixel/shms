<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealGroup extends Model
{
    protected $fillable = [
        'leader_student_id',
        'center_id',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function leader()
    {
        return $this->belongsTo(Student::class, 'leader_student_id');
    }

    public function members()
    {
        return $this->hasMany(MealGroupMember::class, 'group_id');
    }

    public function students()
    {
        return $this->hasManyThrough(Student::class, MealGroupMember::class, 'group_id', 'id', 'id', 'student_id');
    }
}
