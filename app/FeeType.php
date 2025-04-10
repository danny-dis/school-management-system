<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Helpers\AppHelper;

/**
 * Class FeeType
 * 
 * This model represents a fee type in the fee management system.
 * 
 * @package App
 */
class FeeType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'description', 
        'class_id', 
        'amount',
        'status',
        'is_recurring',
        'frequency',
        'due_day'
    ];

    /**
     * Frequency constants
     */
    const FREQUENCY_MONTHLY = 'monthly';
    const FREQUENCY_QUARTERLY = 'quarterly';
    const FREQUENCY_HALF_YEARLY = 'half_yearly';
    const FREQUENCY_YEARLY = 'yearly';

    /**
     * Get the class that owns the fee type.
     */
    public function class()
    {
        return $this->belongsTo('App\IClass');
    }

    /**
     * Get the fee invoices for the fee type.
     */
    public function invoices()
    {
        return $this->hasMany('App\FeeInvoice');
    }

    /**
     * Get the fee type status as text.
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return $this->status == AppHelper::ACTIVE ? 'Active' : 'Inactive';
    }

    /**
     * Get the frequency as text.
     *
     * @return string
     */
    public function getFrequencyTextAttribute()
    {
        switch ($this->frequency) {
            case self::FREQUENCY_MONTHLY:
                return 'Monthly';
            case self::FREQUENCY_QUARTERLY:
                return 'Quarterly';
            case self::FREQUENCY_HALF_YEARLY:
                return 'Half Yearly';
            case self::FREQUENCY_YEARLY:
                return 'Yearly';
            default:
                return 'Unknown';
        }
    }
}
