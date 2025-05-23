<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
//            \Illuminate\Session\Middleware\StartSession::class,
            \App\Http\Middleware\Language::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
//            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
            'api.response',
            'cors',
            'security.headers',
        ],
        'frontend' => [
            \App\Http\Middleware\Frontend::class,
        ]

    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        'permission' => \App\Http\Middleware\PermissionMiddleware::class,
        'module.enabled' => \App\Http\Middleware\ModuleEnabled::class,
        'module.licensed' => \App\Http\Middleware\ModuleLicensed::class,
        'api.role' => \App\Http\Middleware\CheckUserRole::class,
        'student' => \App\Http\Middleware\StudentMiddleware::class,
        'parent' => \App\Http\Middleware\ParentMiddleware::class,
        'module' => \App\Http\Middleware\ModuleEnabled::class,
        'api.response' => \App\Http\Middleware\ApiResponseMiddleware::class,
        'api.rate.limit' => \App\Http\Middleware\ApiRateLimiter::class,
        'api.cache' => \App\Http\Middleware\ApiCache::class,
        'api.logger' => \App\Http\Middleware\ApiLogger::class,
        'cors' => \App\Http\Middleware\Cors::class,
        'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
    ];
}
