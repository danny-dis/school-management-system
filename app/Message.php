<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Message
 * 
 * This model represents a message in the communication system.
 * 
 * @package App
 */
class Message extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
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
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'read_at',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the sender of the message.
     */
    public function sender()
    {
        return $this->belongsTo('App\User', 'sender_id');
    }

    /**
     * Get the receiver of the message.
     */
    public function receiver()
    {
        return $this->belongsTo('App\User', 'receiver_id');
    }

    /**
     * Check if the message is read.
     *
     * @return bool
     */
    public function isRead()
    {
        return $this->read_at !== null;
    }

    /**
     * Mark the message as read.
     *
     * @return void
     */
    public function markAsRead()
    {
        if (!$this->isRead()) {
            $this->read_at = now();
            $this->save();
        }
    }

    /**
     * Delete the message for a user.
     *
     * @param  int  $userId
     * @return void
     */
    public function deleteForUser($userId)
    {
        if ($this->sender_id == $userId) {
            $this->deleted_by_sender = true;
        } elseif ($this->receiver_id == $userId) {
            $this->deleted_by_receiver = true;
        }
        
        $this->save();
        
        // If both users deleted the message, physically delete it
        if ($this->deleted_by_sender && $this->deleted_by_receiver) {
            $this->delete();
        }
    }
}
