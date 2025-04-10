<?php

namespace App\Http\Middleware;

use Closure;
use App\Modules\ModuleManager;

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
            return redirect()->route('user.dashboard')->with('error', 'This module is not enabled. Please contact your administrator.');
        }
        
        return $next($request);
    }
}
