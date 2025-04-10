<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VaccinationRecord
 * 
 * This model represents a vaccination record in the health management system.
 * 
 * @package App
 */
class VaccinationRecord extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'student_id', 
        'vaccination_id', 
        'dose_number', 
        'date_given',
        'next_due_date',
        'administered_by',
        'notes',
        'recorded_by'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'date_given',
        'next_due_date',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the student that owns the vaccination record.
     */
    public function student()
    {
        return $this->belongsTo('App\Registration', 'student_id');
    }

    /**
     * Get the vaccination that owns the vaccination record.
     */
    public function vaccination()
    {
        return $this->belongsTo('App\Vaccination');
    }

    /**
     * Get the user who recorded the vaccination record.
     */
    public function recorder()
    {
        return $this->belongsTo('App\User', 'recorded_by');
    }

    /**
     * Check if the next dose is due.
     *
     * @return bool
     */
    public function isNextDoseDue()
    {
        return $this->next_due_date !== null && $this->next_due_date->isPast();
    }
}
