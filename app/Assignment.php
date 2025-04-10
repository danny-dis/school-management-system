<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Helpers\AppHelper;

/**
 * Class Assignment
 * 
 * This model represents an assignment in a course for the online learning module.
 * 
 * @package App
 */
class Assignment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 
        'description', 
        'course_id', 
        'due_date', 
        'total_marks', 
        'attachment',
        'status',
        'instructions'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'due_date',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the course that owns the assignment.
     */
    public function course()
    {
        return $this->belongsTo('App\Course');
    }

    /**
     * Get the submissions for the assignment.
     */
    public function submissions()
    {
        return $this->hasMany('App\AssignmentSubmission');
    }

    /**
     * Get the assignment status as text.
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return $this->status == AppHelper::ACTIVE ? 'Active' : 'Inactive';
    }
}
