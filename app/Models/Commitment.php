<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commitment extends Model
{
    protected $fillable = [
        'student_id',
        'violation_id',
        'text',
        'date',
        'requires_guardian_signature',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'requires_guardian_signature' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function violation()
    {
        return $this->belongsTo(Violation::class);
    }
}
