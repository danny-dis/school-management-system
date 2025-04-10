<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'subject',
        'message',
        'attachment',
        'read_at',
        'deleted_by_sender',
        'deleted_by_receiver'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'read_at' => 'datetime',
        'deleted_by_sender' => 'boolean',
        'deleted_by_receiver' => 'boolean',
    ];

    /**
     * Get the sender of the message.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver of the message.
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Mark the message as read.
     *
     * @return bool
     */
    public function markAsRead()
    {
        if (!$this->read_at) {
            $this->read_at = now();
            return $this->save();
        }

        return true;
    }

    /**
     * Delete the message for a user.
     *
     * @param int $userId
     * @return bool
     */
    public function deleteForUser($userId)
    {
        if ($this->sender_id == $userId) {
            $this->deleted_by_sender = true;
        } elseif ($this->receiver_id == $userId) {
            $this->deleted_by_receiver = true;
        } else {
            return false;
        }

        // If both sender and receiver have deleted the message, actually delete it
        if ($this->deleted_by_sender && $this->deleted_by_receiver) {
            return $this->delete();
        }

        return $this->save();
    }

    /**
     * Scope a query to only include unread messages.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
}
