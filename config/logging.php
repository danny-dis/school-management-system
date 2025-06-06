<?php

use Monolog\Handler\StreamHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
        ],

        'single' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
        ],
        'smsLog' => [
            'driver' => 'daily',
            'path' => storage_path('logs/sms.log'),
            'level' => 'debug',
        ],
        'api' => [
            'driver' => 'daily',
            'path' => storage_path('logs/api.log'),
            'level' => 'debug',
            'days' => 14,
        ],
        'studentabsentlog' => [
            'driver' => 'daily',
            'path' => storage_path('logs/student-absent-job.log'),
            'level' => 'debug',
        ],
        'employeeabsentlog' => [
            'driver' => 'daily',
            'path' => storage_path('logs/employee-absent-job.log'),
            'level' => 'debug',
        ],
        'studentattendancelog' => [
            'driver' => 'daily',
            'path' => storage_path('logs/student-attendance-upload.log'),
            'level' => 'debug',
        ],
        'employeeattendancelog' => [
            'driver' => 'daily',
            'path' => storage_path('logs/employee-attendance-upload.log'),
            'level' => 'debug',
        ],
        'bulk' => [
            'driver' => 'daily',
            'path' => storage_path('logs/bulk-process.log'),
            'level' => 'debug',
        ],
        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'days' => 7,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],
    ],

];
