<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class LibrarySetting
 * 
 * This model represents the library settings.
 * 
 * @package App
 */
class LibrarySetting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'max_books_per_student', 
        'max_days_per_issue', 
        'fine_per_day', 
        'allow_renewal', 
        'max_renewals'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'allow_renewal' => 'boolean',
    ];

    /**
     * Get the settings.
     *
     * @return \App\LibrarySetting
     */
    public static function getSettings()
    {
        return self::first();
    }
}
