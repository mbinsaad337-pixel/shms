<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FoodQrGroup extends Model
{
    protected $fillable = [
        'center_id',
        'created_by_student_id',
        'qr_code',
        'qr_token',
        'members_count',
        'valid_date',
        'is_used',
        'expires_at',
    ];

    protected $casts = [
        'valid_date' => 'date',
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }
    public function creatorStudent()
    {
        return $this->belongsTo(Student::class, 'created_by_student_id');
    }
    public function members()
    {
        return $this->hasMany(FoodQrGroupMember::class, 'group_id');
    }
    public function distributions()
    {
        return $this->hasMany(FoodDistribution::class, 'qr_group_id');
    }

    public function isValid(): bool
    {
        if ($this->is_used)
            return false;
        if ($this->expires_at && $this->expires_at->isPast())
            return false;
        if (!$this->valid_date->isToday())
            return false;
        return true;
    }

    public static function generateForStudents(Student $creator, array $studentIds, int $centerId): self
    {
        $token = Str::random(32);
        $group = self::create([
            'center_id' => $centerId,
            'created_by_student_id' => $creator->id,
            'qr_code' => 'GRP_' . $token,
            'qr_token' => $token,
            'members_count' => count($studentIds),
            'valid_date' => now()->toDateString(),
            'is_used' => false,
            'expires_at' => now()->endOfDay(),
        ]);

        foreach ($studentIds as $studentId) {
            $subscription = FoodSubscription::where('student_id', $studentId)
                ->where('status', 'active')
                ->first();
            $group->members()->create([
                'student_id' => $studentId,
                'subscription_id' => $subscription?->id,
            ]);
        }

        return $group;
    }
}
