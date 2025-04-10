<?php

namespace App\Modules;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\AppHelper;

/**
 * ModuleManager - Handles the management of system modules
 *
 * This class provides functionality to manage modules in the system,
 * including enabling/disabling modules, checking module status,
 * and retrieving module information.
 *
 * @package App\Modules
 * @author Zophlic Development Team
 */
class ModuleManager
{
    /**
     * Cache key for storing module information
     */
    const CACHE_KEY = 'system_modules';

    /**
     * Cache expiration time in minutes
     */
    const CACHE_EXPIRATION = 1440; // 24 hours

    /**
     * List of available modules in the system
     */
    const AVAILABLE_MODULES = [
        'student_portal' => [
            'name' => 'Student Portal',
            'description' => 'Provides a dedicated portal for students to access their information',
            'default_status' => true,
            'dependencies' => [],
            'version' => '1.0',
        ],
        'parent_portal' => [
            'name' => 'Parent Portal',
            'description' => 'Provides a dedicated portal for parents to monitor their children\'s progress',
            'default_status' => true,
            'dependencies' => ['student_portal'],
            'version' => '1.0',
        ],
        'online_learning' => [
            'name' => 'Online Learning',
            'description' => 'Enables online learning features including course content management and assignments',
            'default_status' => true,
            'dependencies' => ['student_portal'],
            'version' => '1.0',
        ],
        'employees_management' => [
            'name' => 'Employees Management',
            'description' => 'Comprehensive employee management including attendance, leave, and work outside',
            'default_status' => true,
            'dependencies' => [],
            'version' => '1.0',
        ],
        'sms_email' => [
            'name' => 'SMS and Email System',
            'description' => 'SMS Gateway setup, email/SMS templating, and notifications',
            'default_status' => true,
            'dependencies' => [],
            'version' => '1.0',
        ],
        'id_card' => [
            'name' => 'ID Card Management',
            'description' => 'ID card templates and printing for students and employees',
            'default_status' => true,
            'dependencies' => [],
            'version' => '1.0',
        ],
        'online_admission' => [
            'name' => 'Online Admission',
            'description' => 'Online student admission system',
            'default_status' => true,
            'dependencies' => [],
            'version' => '1.0',
        ],
        'online_documents' => [
            'name' => 'Online Documents',
            'description' => 'Online admit cards and payslips',
            'default_status' => true,
            'dependencies' => [],
            'version' => '1.0',
        ],
        'notice_board' => [
            'name' => 'Notice Board',
            'description' => 'School notice board system',
            'default_status' => true,
            'dependencies' => [],
            'version' => '1.0',
        ],
        'accounting' => [
            'name' => 'Accounting System',
            'description' => 'Account management, budget, account heads, and income/expense tracking',
            'default_status' => true,
            'dependencies' => [],
            'version' => '1.0',
        ],
        'student_billing' => [
            'name' => 'Student Billing',
            'description' => 'Student invoice and fee management',
            'default_status' => true,
            'dependencies' => ['accounting'],
            'version' => '1.0',
        ],
        'payroll' => [
            'name' => 'Payroll System',
            'description' => 'Salary templates and employee salary payment',
            'default_status' => true,
            'dependencies' => ['accounting'],
            'version' => '1.0',
        ],
        'hostel' => [
            'name' => 'Hostel Management',
            'description' => 'Hostel and collection management',
            'default_status' => true,
            'dependencies' => [],
            'version' => '1.0',
        ],
        'library' => [
            'name' => 'Library Management',
            'description' => 'Library management, book issuing, and fine collection',
            'default_status' => true,
            'dependencies' => [],
            'version' => '1.0',
        ],
        'academic_calendar' => [
            'name' => 'Academic Calendar',
            'description' => 'Academic calendar management and printing',
            'default_status' => true,
            'dependencies' => [],
            'version' => '1.0',
        ],
        'bulk_communication' => [
            'name' => 'Bulk Communication',
            'description' => 'Bulk SMS and email sending',
            'default_status' => true,
            'dependencies' => ['sms_email'],
            'version' => '1.0',
        ],
        'advanced_reporting' => [
            'name' => 'Advanced Reporting',
            'description' => 'Enhanced reporting capabilities with data visualization',
            'default_status' => true,
            'dependencies' => [],
            'version' => '1.0',
        ],
        'website_management' => [
            'name' => 'Website Management',
            'description' => 'Dynamic front website and management panel',
            'default_status' => true,
            'dependencies' => [],
            'version' => '1.0',
        ],
        'photo_gallery' => [
            'name' => 'Photo Gallery',
            'description' => 'School photo gallery management',
            'default_status' => true,
            'dependencies' => ['website_management'],
            'version' => '1.0',
        ],
        'event_management' => [
            'name' => 'Event Management',
            'description' => 'School event management',
            'default_status' => true,
            'dependencies' => [],
            'version' => '1.0',
        ],
        'analytics' => [
            'name' => 'Analytics',
            'description' => 'Google Analytics integration',
            'default_status' => true,
            'dependencies' => ['website_management'],
            'version' => '1.0',
        ],
    ];

    /**
     * Get all modules with their status
     *
     * @return array Array of modules with their status
     */
    public static function getAllModules()
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_EXPIRATION, function () {
            // Check if the modules table exists
            try {
                $dbModules = DB::table('modules')->get();
                $modules = [];

                // Process modules from database
                foreach (self::AVAILABLE_MODULES as $moduleKey => $moduleInfo) {
                    $dbModule = $dbModules->where('module_key', $moduleKey)->first();

                    $modules[$moduleKey] = [
                        'name' => $moduleInfo['name'],
                        'description' => $moduleInfo['description'],
                        'status' => $dbModule ? (bool)$dbModule->status : $moduleInfo['default_status'],
                        'dependencies' => $moduleInfo['dependencies'],
                        'version' => $moduleInfo['version'],
                    ];
                }

                return $modules;
            } catch (\Exception $e) {
                // If table doesn't exist, return default modules
                $modules = [];
                foreach (self::AVAILABLE_MODULES as $moduleKey => $moduleInfo) {
                    $modules[$moduleKey] = [
                        'name' => $moduleInfo['name'],
                        'description' => $moduleInfo['description'],
                        'status' => $moduleInfo['default_status'],
                        'dependencies' => $moduleInfo['dependencies'],
                        'version' => $moduleInfo['version'],
                    ];
                }

                return $modules;
            }
        });
    }

    /**
     * Check if a module is enabled
     *
     * @param string $moduleKey The module key to check
     * @return bool True if the module is enabled, false otherwise
     */
    public static function isModuleEnabled($moduleKey)
    {
        $modules = self::getAllModules();

        if (!isset($modules[$moduleKey])) {
            return false;
        }

        // Check if the module is enabled
        if (!$modules[$moduleKey]['status']) {
            return false;
        }

        // Check if all dependencies are enabled
        foreach ($modules[$moduleKey]['dependencies'] as $dependency) {
            if (!isset($modules[$dependency]) || !$modules[$dependency]['status']) {
                return false;
            }
        }

        return true;
    }

    /**
     * Enable a module
     *
     * @param string $moduleKey The module key to enable
     * @return bool True if the module was enabled, false otherwise
     */
    public static function enableModule($moduleKey)
    {
        if (!isset(self::AVAILABLE_MODULES[$moduleKey])) {
            return false;
        }

        try {
            DB::table('modules')->updateOrInsert(
                ['module_key' => $moduleKey],
                ['status' => true, 'updated_at' => now()]
            );

            // Clear the cache
            Cache::forget(self::CACHE_KEY);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Disable a module
     *
     * @param string $moduleKey The module key to disable
     * @return bool True if the module was disabled, false otherwise
     */
    public static function disableModule($moduleKey)
    {
        if (!isset(self::AVAILABLE_MODULES[$moduleKey])) {
            return false;
        }

        // Check if any other modules depend on this one
        $modules = self::getAllModules();
        foreach ($modules as $key => $module) {
            if ($key !== $moduleKey && in_array($moduleKey, $module['dependencies']) && $module['status']) {
                return false;
            }
        }

        try {
            DB::table('modules')->updateOrInsert(
                ['module_key' => $moduleKey],
                ['status' => false, 'updated_at' => now()]
            );

            // Clear the cache
            Cache::forget(self::CACHE_KEY);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get module information
     *
     * @param string $moduleKey The module key
     * @return array|null Module information or null if the module doesn't exist
     */
    public static function getModuleInfo($moduleKey)
    {
        $modules = self::getAllModules();

        return isset($modules[$moduleKey]) ? $modules[$moduleKey] : null;
    }

    /**
     * Get enabled modules
     *
     * @return array Array of enabled modules
     */
    public static function getEnabledModules()
    {
        $modules = self::getAllModules();
        $enabledModules = [];

        foreach ($modules as $key => $module) {
            if (self::isModuleEnabled($key)) {
                $enabledModules[$key] = $module;
            }
        }

        return $enabledModules;
    }
}
