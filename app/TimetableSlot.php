<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TimetableSlot
 * 
 * This model represents a slot in a timetable.
 * 
 * @package App
 */
class TimetableSlot extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'timetable_id', 
        'day', 
        'start_time', 
        'end_time', 
        'subject_id',
        'teacher_id',
        'room_id'
    ];

    /**
     * Day constants
     */
    const DAY_SUNDAY = 0;
    const DAY_MONDAY = 1;
    const DAY_TUESDAY = 2;
    const DAY_WEDNESDAY = 3;
    const DAY_THURSDAY = 4;
    const DAY_FRIDAY = 5;
    const DAY_SATURDAY = 6;

    /**
     * Get the timetable that owns the slot.
     */
    public function timetable()
    {
        return $this->belongsTo('App\Timetable');
    }

    /**
     * Get the subject for the slot.
     */
    public function subject()
    {
        return $this->belongsTo('App\Subject');
    }

    /**
     * Get the teacher for the slot.
     */
    public function teacher()
    {
        return $this->belongsTo('App\Employee', 'teacher_id');
    }

    /**
     * Get the room for the slot.
     */
    public function room()
    {
        return $this->belongsTo('App\Room');
    }

    /**
     * Get the day name.
     *
     * @return string
     */
    public function getDayNameAttribute()
    {
        $days = [
            self::DAY_SUNDAY => 'Sunday',
            self::DAY_MONDAY => 'Monday',
            self::DAY_TUESDAY => 'Tuesday',
            self::DAY_WEDNESDAY => 'Wednesday',
            self::DAY_THURSDAY => 'Thursday',
            self::DAY_FRIDAY => 'Friday',
            self::DAY_SATURDAY => 'Saturday'
        ];
        
        return $days[$this->day] ?? 'Unknown';
    }

    /**
     * Get the formatted time range.
     *
     * @return string
     */
    public function getTimeRangeAttribute()
    {
        return date('h:i A', strtotime($this->start_time)) . ' - ' . date('h:i A', strtotime($this->end_time));
    }

    /**
     * Get the duration in minutes.
     *
     * @return int
     */
    public function getDurationAttribute()
    {
        $start = strtotime($this->start_time);
        $end = strtotime($this->end_time);
        
        return ($end - $start) / 60;
    }
}
