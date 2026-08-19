<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FoodDistribution extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'center_id',
        'student_id',
        'subscription_id',
        'qr_group_id',
        'student_qr_group_id',
        'group_name',
        'group_members_count',
        'meal_type',
        'distribution_type',
        'dish_number',
        'scan_type',
        'distributed_by',
        'distributed_at',
        'notes',
    ];

    protected $casts = [
        'distributed_at' => 'datetime',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function subscription()
    {
        return $this->belongsTo(FoodSubscription::class, 'subscription_id');
    }
    public function qrGroup()
    {
        return $this->belongsTo(FoodQrGroup::class, 'qr_group_id');
    }
    public function studentQrGroup()
    {
        return $this->belongsTo(StudentQrGroup::class, 'student_qr_group_id');
    }
    public function distributor()
    {
        return $this->belongsTo(User::class, 'distributed_by');
    }

    public function getMealTypeLabel(): string
    {
        return match ($this->meal_type) {
            'breakfast' => 'فطور',
            'lunch' => 'غداء',
            'dinner' => 'عشاء',
            default => $this->meal_type,
        };
    }

    public function getTypeLabel(): string
    {
        return match ($this->distribution_type) {
            'individual' => 'فردي',
            'group' => 'مجمع',
            'extra' => 'لاحقة',
            default => $this->distribution_type,
        };
    }
}
