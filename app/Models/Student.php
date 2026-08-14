<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'center_id', 'program_id', 'student_number', 'national_id',
        'name_ar', 'name_en', 'surname',
        'date_of_birth', 'place_of_birth',
        'id_card_number', 'id_card_source', 'id_card_date', 'id_card_file',
        'nationality', 'blood_type', 'marital_status', 'health_status',
        'city', 'dependents_count',
        'photo', 'certificate_file',
        // Address
        'governorate', 'district', 'village', 'home_phone',
        'permanent_address', 'phone', 'email',
        // Education
        'last_certificate', 'last_cert_major', 'last_cert_grade', 'graduation_year',
        'graduated_school', 'enrollment_date',
        'university', 'college', 'major', 'academic_level', 'university_id', 'university_card_file',
        'study_duration', 'remaining_period', 'expected_graduation',
        'current_academic_year', 'skills',
        // Guardian
        'guardian_name', 'guardian_relation', 'guardian_education',
        'guardian_phone', 'guardian_job',
        // Emergency
        'emergency_name', 'emergency_relation', 'emergency_phone',
        // Family
        'family_males', 'family_females', 'family_avg_income', 'family_workers',
        // System
        'barcode', 'status', 'annual_fees', 'registration_date',
        'is_profile_approved', 'can_edit_profile', 'is_graduate',
        'profile_step', 'profile_completion',
        // Graduation Workflow
        'graduation_request_status', 'job_title', 'graduation_rejection_reason', 'graduation_requested_at'
    ];

    protected $casts = [
        'date_of_birth'       => 'date',
        'id_card_date'        => 'date',
        'enrollment_date'     => 'date',
        'expected_graduation' => 'date',
        'registration_date'   => 'date',
        'is_profile_approved' => 'boolean',
        'can_edit_profile'    => 'boolean',
        'is_graduate'              => 'boolean',
        'family_workers'           => 'array',
        'graduation_requested_at'  => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function graduationAttachments()
    {
        return $this->hasMany(GraduationAttachment::class);
    }

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function roomAssignments()
    {
        return $this->hasMany(RoomAssignment::class);
    }

    public function room()
    {
        return $this->hasOneThrough(Room::class, RoomAssignment::class, 'student_id', 'id', 'id', 'room_id')
            ->whereNull('released_at');
    }

    public function activeRoomAssignment()
    {
        return $this->hasOne(RoomAssignment::class)->whereNull('released_at');
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }

    public function violations()
    {
        return $this->hasMany(Violation::class);
    }

    public function penalties()
    {
        return $this->hasMany(Penalty::class);
    }

    public function commitments()
    {
        return $this->hasMany(Commitment::class);
    }

    public function mealSubscription()
    {
        return $this->hasOne(MealSubscription::class);
    }

    public function foodSubscriptions()
    {
        return $this->hasMany(\App\Models\FoodSubscription::class, 'student_id');
    }

    public function activeFoodSubscription()
    {
        return $this->hasOne(\App\Models\FoodSubscription::class, 'student_id')
            ->where('status', 'active');
    }

    public function quranCircles()
    {
        return $this->belongsToMany(QuranCircle::class, 'circle_students', 'student_id', 'circle_id')
            ->withPivot(['last_sura', 'last_verse', 'last_progress_at'])
            ->withTimestamps();
    }

    public function circleAttendances()
    {
        return $this->hasMany(CircleAttendance::class);
    }

    public function grades()
    {
        return $this->hasMany(StudentGrade::class);
    }

    public function achievements()
    {
        return $this->hasMany(StudentAchievement::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    public function getTotalPaidAttribute()
    {
        return $this->vouchers()
            ->where('type', 'receipt')
            ->where('status', 'approved')
            ->sum('amount');
    }

    public function getRemainingFeesAttribute()
    {
        return max(0, (float)$this->annual_fees - (float)$this->total_paid);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // ──────────────────────────────────────────────
    // Program Scopes
    // ──────────────────────────────────────────────

    public function scopeAcademic($query)
    {
        return $query->whereHas('program', fn ($q) => $q->where('code', 'academic'));
    }

    public function scopeCooperative($query)
    {
        return $query->whereHas('program', fn ($q) => $q->where('code', 'cooperative'));
    }

    public function scopeByProgram($query, string $code)
    {
        return $query->whereHas('program', fn ($q) => $q->where('code', $code));
    }

    // ──────────────────────────────────────────────
    // Program Feature Helpers
    // ──────────────────────────────────────────────

    /**
     * التحقق من دعم ميزة معينة بناءً على برنامج الطالب.
     * المفاتيح: activities | attendance | violations | evaluation | quran_circle | leaves
     */
    public function allows(string $feature): bool
    {
        return $this->program?->allows($feature) ?? false;
    }

    public function isAcademic(): bool
    {
        return $this->program?->code === 'academic';
    }

    public function isCooperative(): bool
    {
        return $this->program?->code === 'cooperative';
    }
}
