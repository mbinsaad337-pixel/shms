<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyMenu extends Model
{
    protected $fillable = [
        'center_id',
        'week_start_date',
        'is_published',
        'published_at',
        'created_by',
    ];

    protected $casts = [
        'week_start_date' => 'date',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
