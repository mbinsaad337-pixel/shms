<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\FilterBySupervisor;

class StudentAchievement extends Model
{
    use FilterBySupervisor, SoftDeletes;
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
