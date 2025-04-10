<?php

namespace App\Http\Middleware;

use Closure;
use App\Facades\ModuleManager;
use Illuminate\Support\Facades\Auth;

/**
 * ModuleEnabled Middleware
 *
 * This middleware checks if a specific module is enabled before allowing
 * access to a route. If the module is disabled, the user is redirected
 * to the dashboard with an error message.
 *
 * @package App\Http\Middleware
 * @author Zophlic Development Team
 */
class ModuleEnabled
{
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
        // Check if the module is enabled
        if (!ModuleManager::isModuleEnabled($moduleKey)) {
            // If it's an API request, return a JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This module is not enabled. Please contact your administrator.'
                ], 403);
            }

            // For web requests, redirect with an error message
            return redirect()->route('user.dashboard')->with('error', 'This module is not enabled. Please contact your administrator.');
        }

        // For super admins, always allow access regardless of module status
        if (Auth::check() && Auth::user()->is_super_admin) {
            return $next($request);
        }

        return $next($request);
    }
}
