<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'center_id',
        'created_by',
        'title',
        'body',
        'video_url',
        'video_path',
        'cover_image',
        'gallery',
        'category',
        'status',
        'rejection_reason',
        'is_published',
        'published_at',
    ];

    public function comments()
    {
        return $this->hasMany(NewsComment::class)->latest();
    }

    public function likes()
    {
        return $this->hasMany(NewsLike::class);
    }

    public function isLikedBy($user): bool
    {
        if (!$user) return false;
        return $this->likes()->where('user_id', is_object($user) ? $user->id : $user)->exists();
    }

    protected $casts = [
        'gallery' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getCategoryLabel(): string
    {
        return match ($this->category) {
            'sports' => 'رياضي',
            'culture' => 'ثقافي',
            'achievement' => 'إنجاز',
            default => 'عام',
        };
    }

    public function getCategoryColor(): string
    {
        return match ($this->category) {
            'sports' => 'blue',
            'culture' => 'purple',
            'achievement' => 'gold',
            default => 'navy',
        };
    }

    public function isPending(): bool
    {
        return ($this->status ?? 'pending') === 'pending' && !$this->is_published;
    }

    public function isApproved(): bool
    {
        return ($this->status ?? 'approved') === 'approved' || $this->is_published;
    }

    public function isRejected(): bool
    {
        return ($this->status ?? '') === 'rejected';
    }

    public function getStatusLabel(): string
    {
        if ($this->isRejected()) {
            return 'مرفوض';
        }
        if ($this->isApproved() || $this->is_published) {
            return 'منشور (معتمد)';
        }
        return 'في الانتظار';
    }

    public function getStatusBadgeClass(): string
    {
        if ($this->isRejected()) {
            return 'bg-rose-100 text-rose-700 border-rose-200';
        }
        if ($this->isApproved() || $this->is_published) {
            return 'bg-emerald-100 text-emerald-700 border-emerald-200';
        }
        return 'bg-amber-100 text-amber-700 border-amber-200';
    }
}
