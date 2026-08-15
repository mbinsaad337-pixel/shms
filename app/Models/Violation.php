<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterBySupervisor;

class Violation extends Model
{
    use FilterBySupervisor;
    protected $fillable = [
        'student_id',
        'center_id',
        'type',
        'severity',
        'description',
        'violation_date',
        'attachments',
        'recorded_by',
    ];

    protected $casts = [
        'violation_date' => 'datetime',
        'attachments' => 'json',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function penalty()
    {
        return $this->hasOne(Penalty::class);
    }

    public function commitment()
    {
        return $this->hasOne(Commitment::class);
    }
}
