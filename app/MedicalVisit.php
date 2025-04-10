<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MedicalVisit
 * 
 * This model represents a medical visit in the health management system.
 * 
 * @package App
 */
class MedicalVisit extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'student_id', 
        'health_record_id', 
        'visit_date', 
        'symptoms',
        'diagnosis',
        'treatment',
        'prescribed_medications',
        'temperature',
        'notes',
        'follow_up_date',
        'attended_by'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'visit_date',
        'follow_up_date',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the student that owns the medical visit.
     */
    public function student()
    {
        return $this->belongsTo('App\Registration', 'student_id');
    }

    /**
     * Get the health record that owns the medical visit.
     */
    public function healthRecord()
    {
        return $this->belongsTo('App\HealthRecord', 'health_record_id');
    }

    /**
     * Get the user who attended the medical visit.
     */
    public function attendedBy()
    {
        return $this->belongsTo('App\User', 'attended_by');
    }

    /**
     * Get the formatted temperature.
     *
     * @return string|null
     */
    public function getFormattedTemperatureAttribute()
    {
        return $this->temperature ? $this->temperature . ' °C' : null;
    }

    /**
     * Check if the visit has a follow-up.
     *
     * @return bool
     */
    public function hasFollowUp()
    {
        return $this->follow_up_date !== null;
    }

    /**
     * Check if the follow-up is due.
     *
     * @return bool
     */
    public function isFollowUpDue()
    {
        return $this->hasFollowUp() && $this->follow_up_date->isPast();
    }
}
