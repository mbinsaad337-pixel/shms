<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAchievement extends Model
{
    protected $fillable = [
        'student_id',
        'title',
        'description',
        'achievement_date',
        'certificate_file',
    ];

    protected $casts = [
        'achievement_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
