<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\LicensingService;
use Illuminate\Support\Facades\Auth;

class ModuleLicensed
{
    /**
     * @var LicensingService
     */
    protected $licensingService;

    /**
     * Create a new middleware instance.
     *
     * @param LicensingService $licensingService
     * @return void
     */
    public function __construct(LicensingService $licensingService)
    {
        $this->licensingService = $licensingService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $moduleKey
     * @return mixed
     */
    public function handle($request, Closure $next, $moduleKey)
    {
        // For super admins, always allow access regardless of license
        if (Auth::check() && Auth::user()->is_super_admin) {
            return $next($request);
        }
        
        // Check if the module is licensed
        if (!$this->licensingService->isModuleLicensed($moduleKey)) {
            // If it's an API request, return a JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This module is not licensed. Please contact your administrator.'
                ], 403);
            }
            
            // For web requests, redirect with an error message
            return redirect()->route('user.dashboard')->with('error', 'This module is not licensed. Please contact your administrator.');
        }
        
        return $next($request);
    }
}
