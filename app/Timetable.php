<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Helpers\AppHelper;

/**
 * Class Timetable
 * 
 * This model represents a timetable in the scheduling system.
 * Optimized by Zophlic for better performance and flexibility.
 * 
 * @package App
 */
class Timetable extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'class_id', 
        'section_id', 
        'academic_year_id', 
        'status',
        'created_by',
        'description'
    ];

    /**
     * Get the class that owns the timetable.
     */
    public function class()
    {
        return $this->belongsTo('App\IClass');
    }

    /**
     * Get the section that owns the timetable.
     */
    public function section()
    {
        return $this->belongsTo('App\Section');
    }

    /**
     * Get the academic year that owns the timetable.
     */
    public function academicYear()
    {
        return $this->belongsTo('App\AcademicYear');
    }

    /**
     * Get the user who created the timetable.
     */
    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * Get the slots for the timetable.
     */
    public function slots()
    {
        return $this->hasMany('App\TimetableSlot');
    }

    /**
     * Get the timetable status as text.
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return $this->status == AppHelper::ACTIVE ? 'Active' : 'Inactive';
    }
}
