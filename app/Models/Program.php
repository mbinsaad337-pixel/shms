<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'allows_activities',
        'allows_attendance',
        'allows_violations',
        'allows_evaluation',
        'allows_quran_circle',
        'allows_leaves',
        'requires_academic_data',
        'nutrition_policy',
        'is_active',
    ];

    protected $casts = [
        'allows_activities'      => 'boolean',
        'allows_attendance'      => 'boolean',
        'allows_violations'      => 'boolean',
        'allows_evaluation'      => 'boolean',
        'allows_quran_circle'    => 'boolean',
        'allows_leaves'          => 'boolean',
        'requires_academic_data' => 'boolean',
        'is_active'              => 'boolean',
    ];

    // ──────────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ──────────────────────────────────────────────
    // Relations
    // ──────────────────────────────────────────────

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    // ──────────────────────────────────────────────
    // Feature Check Helpers
    // ──────────────────────────────────────────────

    /**
     * التحقق من دعم ميزة معينة في هذا البرنامج.
     *
     * المفاتيح المدعومة:
     *   activities | attendance | violations | evaluation | quran_circle | leaves
     */
    public function allows(string $feature): bool
    {
        $map = [
            'activities'   => 'allows_activities',
            'attendance'   => 'allows_attendance',
            'violations'   => 'allows_violations',
            'evaluation'   => 'allows_evaluation',
            'quran_circle' => 'allows_quran_circle',
            'leaves'       => 'allows_leaves',
        ];

        if (! isset($map[$feature])) {
            return false;
        }

        return (bool) $this->{$map[$feature]};
    }

    // ──────────────────────────────────────────────
    // Nutrition Policy Helpers
    // ──────────────────────────────────────────────

    public function isNutritionMandatory(): bool
    {
        return $this->nutrition_policy === 'mandatory';
    }

    public function isNutritionOptional(): bool
    {
        return $this->nutrition_policy === 'optional';
    }

    public function isNutritionNone(): bool
    {
        return $this->nutrition_policy === 'none';
    }

    // ──────────────────────────────────────────────
    // Type Helpers
    // ──────────────────────────────────────────────

    public function isAcademic(): bool
    {
        return $this->code === 'academic';
    }

    public function isCooperative(): bool
    {
        return $this->code === 'cooperative';
    }

    // ──────────────────────────────────────────────
    // Badge Color (for UI)
    // ──────────────────────────────────────────────

    public function getBadgeColorAttribute(): string
    {
        return match ($this->code) {
            'academic'    => 'primary',
            'cooperative' => 'success',
            'summer'      => 'warning',
            'visitor'     => 'secondary',
            default       => 'info',
        };
    }
}
