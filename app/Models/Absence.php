<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterBySupervisor;

class Absence extends Model
{
    use FilterBySupervisor;
    protected $fillable = [
        'student_id',
        'date',
        'absence_type',
        'has_excuse',
        'excuse_type',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
        'has_excuse' => 'boolean',
    ];

    public static $absenceTypeLabels = [
        'housing' => 'غياب سكن',
        'quran' => 'غياب حلقة قرآنية',
        'activity' => 'غياب نشاط',
        'other' => 'غياب آخر',
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
