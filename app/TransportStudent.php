<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TransportStudent
 * 
 * This model represents a student using the transportation system.
 * 
 * @package App
 */
class TransportStudent extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'student_id', 
        'route_id', 
        'stop_id', 
        'start_date',
        'end_date',
        'status',
        'notes'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'start_date',
        'end_date',
        'created_at',
        'updated_at'
    ];

    /**
     * Status constants
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_EXPIRED = 'expired';

    /**
     * Get the student that owns the transport record.
     */
    public function student()
    {
        return $this->belongsTo('App\Registration', 'student_id');
    }

    /**
     * Get the route that owns the transport record.
     */
    public function route()
    {
        return $this->belongsTo('App\TransportRoute', 'route_id');
    }

    /**
     * Get the stop that owns the transport record.
     */
    public function stop()
    {
        return $this->belongsTo('App\TransportStop', 'stop_id');
    }

    /**
     * Get the status as text.
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case self::STATUS_ACTIVE:
                return 'Active';
            case self::STATUS_INACTIVE:
                return 'Inactive';
            case self::STATUS_EXPIRED:
                return 'Expired';
            default:
                return 'Unknown';
        }
    }

    /**
     * Get the status color class.
     *
     * @return string
     */
    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case self::STATUS_ACTIVE:
                return 'success';
            case self::STATUS_INACTIVE:
                return 'warning';
            case self::STATUS_EXPIRED:
                return 'danger';
            default:
                return 'default';
        }
    }

    /**
     * Check if the transport record is active.
     *
     * @return bool
     */
    public function isActive()
    {
        $now = now();
        return $this->status == self::STATUS_ACTIVE && 
               $this->start_date <= $now && 
               ($this->end_date === null || $this->end_date >= $now);
    }
}
