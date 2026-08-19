<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'center_id',
        'club_id',
        'name',
        'category',
        'description',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'location',
        'target_audience',
        'max_participants',
        'budget',
        'total_cost',
        'attachment_pdf',
        'fund_id',
        'status',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function targetedStudents()
    {
        return $this->belongsToMany(Student::class, 'activity_student_targets');
    }

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function participants()
    {
        return $this->hasMany(ActivityParticipant::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }
}
