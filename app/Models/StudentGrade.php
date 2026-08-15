<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterBySupervisor;

class StudentGrade extends Model
{
    use FilterBySupervisor;
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
