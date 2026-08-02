<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $fillable = [
        'student_id',
        'submitted_by_student',
        'type',
        'departure_date',
        'departure_time',
        'expected_return_date',
        'expected_return_time',
        'actual_return_date',
        'reason',
        'status',
        'approved_by',
        'rejection_reason',
        'converted_to_violation',
        'violation_id',
    ];

    protected $casts = [
        'departure_date' => 'datetime',
        'expected_return_date' => 'datetime',
        'actual_return_date' => 'datetime',
        'submitted_by_student' => 'boolean',
        'converted_to_violation' => 'boolean',
    ];

    public static $typeLabels = [
        'temporary' => 'استئذان مؤقت',
        'vacation' => 'إجازة',
        'medical' => 'إجازة طبية',
        'lateness' => 'تأخير',
    ];

    public static $statusLabels = [
        'pending' => 'قيد الانتظار',
        'approved' => 'موافق عليه',
        'rejected' => 'مرفوض',
        'returned' => 'عاد',
        'not_returned' => 'لم يعد',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function violation()
    {
        return $this->belongsTo(Violation::class);
    }

    public function getTypeLabel(): string
    {
        return self::$typeLabels[$this->type] ?? $this->type;
    }

    public function getStatusLabel(): string
    {
        return self::$statusLabels[$this->status] ?? $this->status;
    }
}
