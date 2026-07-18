<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'weekly_menu_id',
        'day_of_week',
        'meal_type',
        'items',
        'notes',
    ];

    protected $casts = [
        'items' => 'json',
    ];

    public function weeklyMenu()
    {
        return $this->belongsTo(WeeklyMenu::class);
    }
}
