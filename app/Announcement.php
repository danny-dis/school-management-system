<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Helpers\AppHelper;

/**
 * Class Announcement
 * 
 * This model represents an announcement in the communication system.
 * 
 * @package App
 */
class Announcement extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 
        'description', 
        'created_by', 
        'start_date', 
        'end_date',
        'for_students',
        'for_teachers',
        'for_parents',
        'for_admins',
        'status'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'start_date',
        'end_date',
        'created_at',
        'updated_at'
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
     * Get the user who created the announcement.
     */
    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * Get the announcement status as text.
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return $this->status == AppHelper::ACTIVE ? 'Active' : 'Inactive';
    }

    /**
     * Check if the announcement is active.
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
     * Check if the announcement is for a specific role.
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
}
