<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Notification
 * 
 * This model represents a notification in the communication system.
 * 
 * @package App
 */
class Notification extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 
        'title', 
        'message', 
        'link', 
        'read',
        'type'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'read' => 'boolean',
    ];

    /**
     * Notification types
     */
    const TYPE_INFO = 'info';
    const TYPE_SUCCESS = 'success';
    const TYPE_WARNING = 'warning';
    const TYPE_DANGER = 'danger';

    /**
     * Get the user that owns the notification.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Mark the notification as read.
     *
     * @return void
     */
    public function markAsRead()
    {
        $this->read = true;
        $this->save();
    }

    /**
     * Get the notification type icon.
     *
     * @return string
     */
    public function getIconAttribute()
    {
        switch ($this->type) {
            case self::TYPE_INFO:
                return 'fa fa-info-circle text-info';
            case self::TYPE_SUCCESS:
                return 'fa fa-check-circle text-success';
            case self::TYPE_WARNING:
                return 'fa fa-exclamation-triangle text-warning';
            case self::TYPE_DANGER:
                return 'fa fa-times-circle text-danger';
            default:
                return 'fa fa-bell';
        }
    }
}
