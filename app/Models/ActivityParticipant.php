<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityParticipant extends Model
{
    use SoftDeletes;
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
