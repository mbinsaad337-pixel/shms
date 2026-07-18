<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CircleSession extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'circle_id',
        'session_date',
        'title',
        'notes',
    ];

    protected $casts = [
        'session_date' => 'date',
    ];

    public function circle()
    {
        return $this->belongsTo(QuranCircle::class);
    }

    public function attendance()
    {
        return $this->hasMany(CircleAttendance::class, 'session_id');
    }

    public function presentStudents()
    {
        return $this->hasMany(CircleAttendance::class, 'session_id')->where('status', 'present');
    }

    public function absentStudents()
    {
        return $this->hasMany(CircleAttendance::class, 'session_id')->where('status', 'absent');
    }
}
