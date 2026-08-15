<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GraduationAttachment extends Model
{
    protected $fillable = [
        'student_id',
        'name',
        'file_path',
        'file_type',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
