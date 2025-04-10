<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'module_key',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'boolean',
    ];
    
    /**
     * Module keys
     */
    const ONLINE_LEARNING = 'online_learning';
    const FEE_MANAGEMENT = 'fee_management';
    const LIBRARY = 'library';
    const MOBILE_APP = 'mobile_app';
    const ADVANCED_REPORTING = 'advanced_reporting';
    const COMMUNICATION = 'communication';
    const TIMETABLE = 'timetable';
    const TRANSPORTATION = 'transportation';
    const HEALTH_RECORDS = 'health_records';
    
    /**
     * Get all available modules
     *
     * @return array
     */
    public static function getAvailableModules()
    {
        return [
            self::ONLINE_LEARNING => 'Online Learning',
            self::FEE_MANAGEMENT => 'Fee Management',
            self::LIBRARY => 'Library',
            self::MOBILE_APP => 'Mobile App',
            self::ADVANCED_REPORTING => 'Advanced Reporting',
            self::COMMUNICATION => 'Communication',
            self::TIMETABLE => 'Timetable',
            self::TRANSPORTATION => 'Transportation',
            self::HEALTH_RECORDS => 'Health Records',
        ];
    }
    
    /**
     * Get module name
     *
     * @return string
     */
    public function getNameAttribute()
    {
        $modules = self::getAvailableModules();
        return $modules[$this->module_key] ?? $this->module_key;
    }
}
