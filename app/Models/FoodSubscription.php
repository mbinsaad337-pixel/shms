<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FoodSubscription extends Model
{
    use SoftDeletes;

    /** @var Carbon $start_date */
    /** @var Carbon $end_date */
    /** @var Carbon $last_payment_date */


    protected $fillable = [
        'center_id',
        'student_id',
        'budget_id',
        'subscription_type',
        'start_date',
        'end_date',
        'days_count',
        'last_payment_date',
        'daily_rate',
        'total_due',
        'total_paid',
        'status',
        'suspended_reason',
        'rejection_reason',
        'qr_code',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'last_payment_date' => 'date',
        'total_due' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'daily_rate' => 'decimal:2',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function budget()
    {
        return $this->belongsTo(FoodBudget::class, 'budget_id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function distributions()
    {
        return $this->hasMany(FoodDistribution::class, 'subscription_id');
    }

    public function getNetBalanceAttribute(): float
    {
        return $this->total_paid - $this->total_due;
    }

    public function getTypeLabel(): string
    {
        return match ($this->subscription_type) {
            'daily' => 'يومي',
            'semi_monthly' => 'نصف شهري',
            'monthly' => 'شهري',
            default => $this->subscription_type,
        };
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'قيد المراجعة',
            'active' => 'فعال',
            'suspended' => 'موقوف',
            'expired' => 'منتهي',
            'cancelled' => 'ملغي',
            'rejected' => 'مرفوض',
            default => $this->status,
        };
    }

    public function generateQrCode(): string
    {
        $token = 'FOOD_SUB_' . $this->id . '_' . $this->student_id . '_' . Str::random(12);
        $this->update(['qr_code' => $token]);
        return $token;
    }

    /**
     * Check if the subscription should be suspended due to date or usage.
     * Returns a warning message if close to expiration (3 days).
     */
    public function checkAndAutoSuspend(): ?string
    {
        if ($this->status !== 'active') {
            return null;
        }

        // 1. Check Usage (Days Count)
        $usedDays = $this->distributions()->count();
        if ($usedDays >= $this->days_count) {
            $this->update([
                'status' => 'suspended',
                'suspended_reason' => 'انتهاء عدد أيام الاشتراك (' . $usedDays . ' وجبات)'
            ]);
            return null;
        }

        // 2. Check Payment Deadline
        if ($this->last_payment_date && $this->last_payment_date->isPast()) {
            $this->update([
                'status' => 'suspended',
                'suspended_reason' => 'تجاوز تاريخ آخر موعد للدفع (' . $this->last_payment_date->format('Y-m-d') . ')'
            ]);
            return null;
        }

        // 3. Check for Warnings (3 days before deadline)
        if ($this->last_payment_date && $this->last_payment_date->diffInDays(now()) <= 3) {
            return "تنبيه: اشتراك التغذية الخاص بك سينتهي/يتوقف خلال " . (int)$this->last_payment_date->diffInDays(now()) . " أيام. يرجى سداد الرسوم.";
        }

        // 4. Check for Warnings (3 days before usage limit - assuming 1 meal per day)
        $remaining = $this->days_count - $usedDays;
        if ($remaining <= 3) {
            return "تنبيه: متبقي لك " . $remaining . " وجبات فقط في اشتراك التغذية.";
        }

        return null;
    }
}
