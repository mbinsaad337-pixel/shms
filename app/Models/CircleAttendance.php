<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CircleAttendance extends Model
{
    protected $table = 'circle_attendance';

    protected $fillable = [
        'session_id',
        'student_id',
        'status',
        'sura',
        'verse',
        'is_handled',
    ];

    public function session()
    {
        return $this->belongsTo(CircleSession::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
