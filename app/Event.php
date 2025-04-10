<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hrshadhin\Userstamps\UserstampsTrait;
use App\Http\Helpers\AppHelper;

/**
 * Class Event
 *
 * This model represents an event in the scheduling system.
 * Enhanced by Zophlic for better scheduling capabilities.
 *
 * @package App
 */
class Event extends Model
{
    use SoftDeletes;
    use UserstampsTrait;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'event_time',
        'start_date',
        'end_date',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_time',
        'title',
        'description',
        'cover_photo',
        'cover_video',
        'tags',
        'slider_1',
        'slider_2',
        'slider_3',
        'slug',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'location',
        'for_students',
        'for_teachers',
        'for_parents',
        'for_admins',
        'status'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'for_students' => 'boolean',
        'for_teachers' => 'boolean',
        'for_parents' => 'boolean',
        'for_admins' => 'boolean',
    ];

    /**
     * Get the event status as text.
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return $this->status == AppHelper::ACTIVE ? 'Active' : 'Inactive';
    }

    /**
     * Check if the event is active.
     *
     * @return bool
     */
    public function isActive()
    {
        $now = now();
        return $this->status == AppHelper::ACTIVE &&
               $this->start_date <= $now &&
               ($this->end_date === null || $this->end_date >= $now);
    }

    /**
     * Check if the event is for a specific role.
     *
     * @param  string  $role
     * @return bool
     */
    public function isForRole($role)
    {
        switch ($role) {
            case 'Student':
                return $this->for_students;
            case 'Teacher':
                return $this->for_teachers;
            case 'Parent':
                return $this->for_parents;
            case 'Admin':
                return $this->for_admins;
            default:
                return false;
        }
    }

    /**
     * Get the formatted date range.
     *
     * @return string
     */
    public function getDateRangeAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return $this->event_time ? $this->event_time->format('M d, Y') : '';
        }

        if ($this->start_date->format('Y-m-d') == $this->end_date->format('Y-m-d')) {
            return $this->start_date->format('M d, Y');
        }

        return $this->start_date->format('M d, Y') . ' - ' . $this->end_date->format('M d, Y');
    }

    /**
     * Get the formatted time range.
     *
     * @return string
     */
    public function getTimeRangeAttribute()
    {
        if (!$this->start_time || !$this->end_time) {
            return 'All Day';
        }

        return date('h:i A', strtotime($this->start_time)) . ' - ' . date('h:i A', strtotime($this->end_time));
    }
}
