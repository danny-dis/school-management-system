<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AssignmentSubmission
 * 
 * This model represents a student's submission for an assignment.
 * 
 * @package App
 */
class AssignmentSubmission extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'assignment_id', 
        'registration_id', 
        'submission_text', 
        'attachment', 
        'submitted_at', 
        'marks',
        'feedback',
        'graded_at',
        'graded_by'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'submitted_at',
        'graded_at',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the assignment that owns the submission.
     */
    public function assignment()
    {
        return $this->belongsTo('App\Assignment');
    }

    /**
     * Get the student that owns the submission.
     */
    public function student()
    {
        return $this->belongsTo('App\Registration', 'registration_id');
    }

    /**
     * Get the teacher who graded the submission.
     */
    public function gradedBy()
    {
        return $this->belongsTo('App\User', 'graded_by');
    }

    /**
     * Check if the submission is late.
     *
     * @return bool
     */
    public function isLate()
    {
        return $this->submitted_at > $this->assignment->due_date;
    }

    /**
     * Check if the submission is graded.
     *
     * @return bool
     */
    public function isGraded()
    {
        return !is_null($this->graded_at);
    }
}
