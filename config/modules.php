<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Module Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the modules system.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Available Modules
    |--------------------------------------------------------------------------
    |
    | This is a list of all available modules in the system.
    |
    */
    'available' => [
        'online_learning' => [
            'name' => 'Online Learning',
            'description' => 'Manage online courses, lessons, assignments, and student progress.',
            'icon' => 'fa-laptop',
            'dependencies' => [],
        ],
        'fee_management' => [
            'name' => 'Fee Management',
            'description' => 'Manage fee types, invoices, payments, and financial reports.',
            'icon' => 'fa-money-bill-wave',
            'dependencies' => [],
        ],
        'library' => [
            'name' => 'Library',
            'description' => 'Manage books, categories, issues, returns, and library settings.',
            'icon' => 'fa-book',
            'dependencies' => [],
        ],
        'mobile_app' => [
            'name' => 'Mobile App Integration',
            'description' => 'Enable mobile app access for students, teachers, and parents.',
            'icon' => 'fa-mobile-alt',
            'dependencies' => [],
        ],
        'advanced_reporting' => [
            'name' => 'Advanced Reporting',
            'description' => 'Generate comprehensive reports and analytics.',
            'icon' => 'fa-chart-bar',
            'dependencies' => [],
        ],
        'communication' => [
            'name' => 'Communication',
            'description' => 'Manage messages, notifications, announcements, and emails.',
            'icon' => 'fa-comments',
            'dependencies' => [],
        ],
        'timetable' => [
            'name' => 'Timetable',
            'description' => 'Manage class schedules, rooms, and teacher assignments.',
            'icon' => 'fa-calendar-alt',
            'dependencies' => [],
        ],
        'transportation' => [
            'name' => 'Transportation',
            'description' => 'Manage vehicles, routes, stops, and student assignments.',
            'icon' => 'fa-bus',
            'dependencies' => [],
        ],
        'health_records' => [
            'name' => 'Health Records',
            'description' => 'Manage student health information, medical visits, and vaccinations.',
            'icon' => 'fa-heartbeat',
            'dependencies' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Enabled Modules
    |--------------------------------------------------------------------------
    |
    | These modules will be enabled by default when the application is installed.
    |
    */
    'default_enabled' => [
        'online_learning',
        'fee_management',
        'library',
        'communication',
        'timetable',
    ],
];
