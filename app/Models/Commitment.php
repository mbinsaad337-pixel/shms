<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\FilterBySupervisor;

class Commitment extends Model
{
    use FilterBySupervisor, SoftDeletes;
    protected $fillable = [
        'student_id',
        'violation_id',
        'title',
        'text',
        'date',
        'requires_guardian_signature',
        'image_path',
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
