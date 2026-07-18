<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    protected $fillable = [
        'student_id',
        'date',
        'has_excuse',
        'excuse_type',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
        'has_excuse' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
