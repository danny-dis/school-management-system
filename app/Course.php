<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Helpers\AppHelper;

/**
 * Class Course
 * 
 * This model represents a course in the online learning module.
 * 
 * @package App
 */
class Course extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'code', 
        'description', 
        'class_id', 
        'teacher_id', 
        'status',
        'cover_image',
        'syllabus',
        'start_date',
        'end_date'
    ];

    /**
     * Get the class that owns the course.
     */
    public function class()
    {
        return $this->belongsTo('App\IClass');
    }

    /**
     * Get the teacher that owns the course.
     */
    public function teacher()
    {
        return $this->belongsTo('App\Employee', 'teacher_id');
    }

    /**
     * Get the lessons for the course.
     */
    public function lessons()
    {
        return $this->hasMany('App\Lesson');
    }

    /**
     * Get the assignments for the course.
     */
    public function assignments()
    {
        return $this->hasMany('App\Assignment');
    }

    /**
     * Get the students enrolled in the course.
     */
    public function students()
    {
        return $this->belongsToMany('App\Registration', 'course_student', 'course_id', 'registration_id')
            ->withTimestamps();
    }

    /**
     * Get the course status as text.
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return $this->status == AppHelper::ACTIVE ? 'Active' : 'Inactive';
    }
}
