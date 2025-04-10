<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Helpers\AppHelper;

/**
 * Class TransportRoute
 * 
 * This model represents a transport route in the transportation system.
 * 
 * @package App
 */
class TransportRoute extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'vehicle_id', 
        'start_place', 
        'start_time',
        'end_place',
        'end_time',
        'distance',
        'fare',
        'status',
        'description'
    ];

    /**
     * Get the vehicle that owns the route.
     */
    public function vehicle()
    {
        return $this->belongsTo('App\Vehicle');
    }

    /**
     * Get the stops for the route.
     */
    public function stops()
    {
        return $this->hasMany('App\TransportStop');
    }

    /**
     * Get the students for the route.
     */
    public function students()
    {
        return $this->hasMany('App\TransportStudent');
    }

    /**
     * Get the route status as text.
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return $this->status == AppHelper::ACTIVE ? 'Active' : 'Inactive';
    }

    /**
     * Get the formatted time range.
     *
     * @return string
     */
    public function getTimeRangeAttribute()
    {
        return date('h:i A', strtotime($this->start_time)) . ' - ' . date('h:i A', strtotime($this->end_time));
    }
}
