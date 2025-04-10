<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Helpers\AppHelper;

/**
 * Class Vaccination
 * 
 * This model represents a vaccination in the health management system.
 * 
 * @package App
 */
class Vaccination extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'description', 
        'recommended_age', 
        'doses',
        'status'
    ];

    /**
     * Get the vaccination records for the vaccination.
     */
    public function records()
    {
        return $this->hasMany('App\VaccinationRecord');
    }

    /**
     * Get the vaccination status as text.
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return $this->status == AppHelper::ACTIVE ? 'Active' : 'Inactive';
    }
}
