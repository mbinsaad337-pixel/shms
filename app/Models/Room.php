<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'center_id',
        'room_number',
        'apartment',
        'building',
        'floor',
        'capacity',
        'status',
        'type',
        'notes',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function assignments()
    {
        return $this->hasMany(RoomAssignment::class);
    }

    public function students()
    {
        return $this->hasManyThrough(Student::class, RoomAssignment::class, 'room_id', 'id', 'id', 'student_id')
            ->whereNull('room_assignments.released_at');
    }
}
