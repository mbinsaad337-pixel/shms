<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityParticipant extends Model
{
    protected $fillable = [
        'activity_id',
        'student_id',
        'attended',
        'registered_at',
    ];

    protected $casts = [
        'attended' => 'boolean',
        'registered_at' => 'datetime',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
