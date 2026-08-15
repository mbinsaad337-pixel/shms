<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterBySupervisor;

class RoomAssignment extends Model
{
    use FilterBySupervisor;
    protected $fillable = [
        'student_id',
        'room_id',
        'assigned_by',
        'assigned_at',
        'released_at',
        'release_reason',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
