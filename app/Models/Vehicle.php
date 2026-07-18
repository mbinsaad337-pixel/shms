<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'center_id',
        'plate_number',
        'type',
        'model',
        'status',
        'document_photo',
        'student_id',
        'color',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function violations()
    {
        return $this->hasMany(VehicleViolation::class);
    }
}
