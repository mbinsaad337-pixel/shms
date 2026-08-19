<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnualReport extends Model
{
    protected $fillable = [
        'center_id', 'user_id', 'title', 'year', 'file_path', 'file_name', 'file_size',
    ];

    protected $casts = [
        'year'       => 'integer',
        'file_size'  => 'integer',
    ];

    public function center() { return $this->belongsTo(Center::class); }
    public function user()   { return $this->belongsTo(User::class); }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
