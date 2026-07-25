<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Complaint extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'sender_center_id',
        'receiver_center_id',
        'subject',
        'body',
        'attachment',
        'attachment_type',
        'status',
        'priority',
        'parent_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id')->withTrashed();
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id')->withTrashed();
    }

    public function senderCenter()
    {
        return $this->belongsTo(Center::class, 'sender_center_id');
    }

    public function receiverCenter()
    {
        return $this->belongsTo(Center::class, 'receiver_center_id');
    }

    public function parent()
    {
        return $this->belongsTo(Complaint::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Complaint::class, 'parent_id')->orderBy('created_at');
    }

    public function isUnread(): bool
    {
        return $this->status === 'unread';
    }

    public function markAsRead(): void
    {
        if ($this->status === 'unread') {
            $this->update(['status' => 'read', 'read_at' => now()]);
        }
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment ? asset('storage/' . $this->attachment) : null;
    }

    public function getPriorityLabelAttribute(): string
    {
        return $this->priority === 'urgent' ? 'عاجل' : 'عادي';
    }

    public function getPriorityColorAttribute(): string
    {
        return $this->priority === 'urgent' ? 'text-red-600 bg-red-50' : 'text-gray-500 bg-gray-50';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'unread'  => 'غير مقروء',
            'read'    => 'مقروء',
            'replied' => 'تم الرد',
            default   => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'unread'  => 'bg-blue-100 text-blue-700',
            'read'    => 'bg-gray-100 text-gray-600',
            'replied' => 'bg-green-100 text-green-700',
            default   => 'bg-gray-100 text-gray-600',
        };
    }
}
