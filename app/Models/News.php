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
}
