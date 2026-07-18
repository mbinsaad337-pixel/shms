<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuranCircle extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'center_id',
        'teacher_id',
        'name',
        'type',
        'description',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'circle_students', 'circle_id', 'student_id')
            ->withPivot(['last_sura', 'last_verse', 'last_progress_at'])
            ->withTimestamps();
    }

    public function sessions()
    {
        return $this->hasMany(CircleSession::class, 'circle_id');
    }
}
