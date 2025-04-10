<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Helpers\AppHelper;

/**
 * Class HealthRecord
 * 
 * This model represents a health record in the health management system.
 * Developed by Zophlic to enhance student health tracking.
 * 
 * @package App
 */
class HealthRecord extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'student_id', 
        'height', 
        'weight', 
        'bmi',
        'blood_group',
        'blood_pressure',
        'pulse_rate',
        'allergies',
        'medications',
        'past_medical_history',
        'vision_left',
        'vision_right',
        'hearing_left',
        'hearing_right',
        'immunizations',
        'emergency_contact',
        'emergency_phone',
        'notes',
        'recorded_by'
    ];

    /**
     * Get the student that owns the health record.
     */
    public function student()
    {
        return $this->belongsTo('App\Registration', 'student_id');
    }

    /**
     * Get the user who recorded the health record.
     */
    public function recorder()
    {
        return $this->belongsTo('App\User', 'recorded_by');
    }

    /**
     * Get the medical visits for the health record.
     */
    public function visits()
    {
        return $this->hasMany('App\MedicalVisit');
    }

    /**
     * Calculate BMI.
     *
     * @param  float  $height
     * @param  float  $weight
     * @return float|null
     */
    public static function calculateBMI($height, $weight)
    {
        if ($height && $weight && $height > 0) {
            // Convert height from cm to m
            $heightInMeters = $height / 100;
            
            // Calculate BMI: weight (kg) / (height (m) * height (m))
            return round($weight / ($heightInMeters * $heightInMeters), 2);
        }
        
        return null;
    }

    /**
     * Get BMI category.
     *
     * @return string|null
     */
    public function getBmiCategoryAttribute()
    {
        if (!$this->bmi) {
            return null;
        }
        
        if ($this->bmi < 18.5) {
            return 'Underweight';
        } elseif ($this->bmi >= 18.5 && $this->bmi < 25) {
            return 'Normal weight';
        } elseif ($this->bmi >= 25 && $this->bmi < 30) {
            return 'Overweight';
        } else {
            return 'Obese';
        }
    }

    /**
     * Get BMI category color.
     *
     * @return string|null
     */
    public function getBmiColorAttribute()
    {
        if (!$this->bmi) {
            return null;
        }
        
        if ($this->bmi < 18.5) {
            return 'warning';
        } elseif ($this->bmi >= 18.5 && $this->bmi < 25) {
            return 'success';
        } elseif ($this->bmi >= 25 && $this->bmi < 30) {
            return 'warning';
        } else {
            return 'danger';
        }
    }
}
