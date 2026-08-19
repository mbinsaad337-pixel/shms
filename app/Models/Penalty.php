<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\FilterBySupervisor;

class Penalty extends Model
{
    use FilterBySupervisor, SoftDeletes;
    protected $fillable = [
        'student_id',
        'violation_id',
        'type',
        'description',
        'start_date',
        'end_date',
        'is_active',
        'applied_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function violation()
    {
        return $this->belongsTo(Violation::class,'violation_id');
    }

    public function appliedBy()
    {
        return $this->belongsTo(User::class, 'applied_by');
    }
}
