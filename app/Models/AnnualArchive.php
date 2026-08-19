<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnualArchive extends Model
{
    protected $fillable = [
        'rollover_id',
        'center_id',
        'year',
        'module',
        'sub_type',
        'title',
        'record_id',
        'record_date',
        'amount',
        'student_id',
        'student_name',
        'data',
        'archived_files',
    ];

    protected $casts = [
        'data' => 'array',
        'archived_files' => 'array',
        'record_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function rollover()
    {
        return $this->belongsTo(AnnualRollover::class, 'rollover_id');
    }

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
