<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class LessonProgress
 * 
 * This model tracks a student's progress through a lesson.
 * 
 * @package App
 */
class LessonProgress extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lesson_id', 
        'registration_id', 
        'status', 
        'progress_percentage', 
        'last_accessed_at', 
        'completed_at'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'last_accessed_at',
        'completed_at',
        'created_at',
        'updated_at'
    ];

    /**
     * Progress status constants
     */
    const STATUS_NOT_STARTED = 'not_started';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';

    /**
     * Get the lesson that owns the progress.
     */
    public function lesson()
    {
        return $this->belongsTo('App\Lesson');
    }

    /**
     * Get the student that owns the progress.
     */
    public function student()
    {
        return $this->belongsTo('App\Registration', 'registration_id');
    }

    /**
     * Get the status as text.
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case self::STATUS_NOT_STARTED:
                return 'Not Started';
            case self::STATUS_IN_PROGRESS:
                return 'In Progress';
            case self::STATUS_COMPLETED:
                return 'Completed';
            default:
                return 'Unknown';
        }
    }

    /**
     * Check if the lesson is completed.
     *
     * @return bool
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}
