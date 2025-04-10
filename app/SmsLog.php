<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SmsLog
 * 
 * This model represents an SMS log in the communication system.
 * 
 * @package App
 */
class SmsLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'recipient', 
        'message', 
        'status',
        'response',
        'created_by'
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';

    /**
     * Get the user who created the log.
     */
    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * Get the status color class.
     *
     * @return string
     */
    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case self::STATUS_PENDING:
                return 'warning';
            case self::STATUS_SENT:
                return 'success';
            case self::STATUS_FAILED:
                return 'danger';
            default:
                return 'default';
        }
    }
}
