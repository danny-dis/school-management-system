<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TransportStop
 * 
 * This model represents a transport stop in the transportation system.
 * 
 * @package App
 */
class TransportStop extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'route_id', 
        'name', 
        'stop_time', 
        'stop_order',
        'latitude',
        'longitude',
        'description'
    ];

    /**
     * Get the route that owns the stop.
     */
    public function route()
    {
        return $this->belongsTo('App\TransportRoute', 'route_id');
    }

    /**
     * Get the students for the stop.
     */
    public function students()
    {
        return $this->hasMany('App\TransportStudent', 'stop_id');
    }

    /**
     * Get the formatted stop time.
     *
     * @return string
     */
    public function getFormattedTimeAttribute()
    {
        return date('h:i A', strtotime($this->stop_time));
    }
}
