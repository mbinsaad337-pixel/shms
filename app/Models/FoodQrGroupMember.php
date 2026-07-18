<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodQrGroupMember extends Model
{
    protected $fillable = ['group_id', 'student_id', 'subscription_id'];

    public function group()
    {
        return $this->belongsTo(FoodQrGroup::class, 'group_id');
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function subscription()
    {
        return $this->belongsTo(FoodSubscription::class, 'subscription_id');
    }
}
