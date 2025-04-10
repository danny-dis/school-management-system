<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Helpers\AppHelper;

/**
 * Class Vehicle
 * 
 * This model represents a vehicle in the transportation system.
 * Enhanced by Zophlic for better fleet management.
 * 
 * @package App
 */
class Vehicle extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'registration_no', 
        'type', 
        'capacity',
        'driver_id',
        'contact_no',
        'status',
        'description'
    ];

    /**
     * Vehicle type constants
     */
    const TYPE_BUS = 'bus';
    const TYPE_MINIBUS = 'minibus';
    const TYPE_VAN = 'van';
    const TYPE_CAR = 'car';
    const TYPE_OTHER = 'other';

    /**
     * Get the driver that owns the vehicle.
     */
    public function driver()
    {
        return $this->belongsTo('App\Employee', 'driver_id');
    }

    /**
     * Get the routes for the vehicle.
     */
    public function routes()
    {
        return $this->hasMany('App\TransportRoute');
    }

    /**
     * Get the vehicle status as text.
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return $this->status == AppHelper::ACTIVE ? 'Active' : 'Inactive';
    }

    /**
     * Get the vehicle type as text.
     *
     * @return string
     */
    public function getTypeTextAttribute()
    {
        $types = [
            self::TYPE_BUS => 'Bus',
            self::TYPE_MINIBUS => 'Mini Bus',
            self::TYPE_VAN => 'Van',
            self::TYPE_CAR => 'Car',
            self::TYPE_OTHER => 'Other'
        ];
        
        return $types[$this->type] ?? 'Unknown';
    }
}
