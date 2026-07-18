<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentGrade extends Model
{
    protected $fillable = [
        'student_id',
        'file_path',
        'semester',
        'academic_year',
        'gpa_percentage',
        'notes',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
