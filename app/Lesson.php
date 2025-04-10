<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Helpers\AppHelper;

/**
 * Class Lesson
 * 
 * This model represents a lesson in a course for the online learning module.
 * 
 * @package App
 */
class Lesson extends Model
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
        'content', 
        'video_url', 
        'attachment',
        'order',
        'status',
        'duration',
        'is_free'
    ];

    /**
     * Get the course that owns the lesson.
     */
    public function course()
    {
        return $this->belongsTo('App\Course');
    }

    /**
     * Get the resources for the lesson.
     */
    public function resources()
    {
        return $this->hasMany('App\LessonResource');
    }

    /**
     * Get the student progress for the lesson.
     */
    public function progress()
    {
        return $this->hasMany('App\LessonProgress');
    }

    /**
     * Get the lesson status as text.
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return $this->status == AppHelper::ACTIVE ? 'Published' : 'Draft';
    }
}
