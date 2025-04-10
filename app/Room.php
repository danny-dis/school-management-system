<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Helpers\AppHelper;

/**
 * Class Room
 * 
 * This model represents a room in the scheduling system.
 * 
 * @package App
 */
class Room extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'room_no', 
        'capacity', 
        'type',
        'status',
        'description'
    ];

    /**
     * Room type constants
     */
    const TYPE_CLASSROOM = 'classroom';
    const TYPE_LAB = 'lab';
    const TYPE_LIBRARY = 'library';
    const TYPE_COMPUTER_LAB = 'computer_lab';
    const TYPE_SCIENCE_LAB = 'science_lab';
    const TYPE_CONFERENCE = 'conference';
    const TYPE_OTHER = 'other';

    /**
     * Get the timetable slots for the room.
     */
    public function timetableSlots()
    {
        return $this->hasMany('App\TimetableSlot');
    }

    /**
     * Get the room status as text.
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return $this->status == AppHelper::ACTIVE ? 'Active' : 'Inactive';
    }

    /**
     * Get the room type as text.
     *
     * @return string
     */
    public function getTypeTextAttribute()
    {
        $types = [
            self::TYPE_CLASSROOM => 'Classroom',
            self::TYPE_LAB => 'Laboratory',
            self::TYPE_LIBRARY => 'Library',
            self::TYPE_COMPUTER_LAB => 'Computer Lab',
            self::TYPE_SCIENCE_LAB => 'Science Lab',
            self::TYPE_CONFERENCE => 'Conference Room',
            self::TYPE_OTHER => 'Other'
        ];
        
        return $types[$this->type] ?? 'Unknown';
    }
}
