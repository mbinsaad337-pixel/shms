<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentQrGroup extends Model
{
    protected $fillable = [
        'primary_student_id',
        'group_token',
        'json_data',
        'is_link_only',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'is_link_only' => 'boolean',
        'expires_at' => 'datetime',
        'json_data' => 'array',
    ];

    public function primaryStudent()
    {
        return $this->belongsTo(Student::class, 'primary_student_id');
    }

    public function members()
    {
        return $this->hasMany(StudentQrGroupMember::class, 'group_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_qr_group_members', 'group_id', 'student_id');
    }
}
