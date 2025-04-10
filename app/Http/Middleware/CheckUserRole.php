<?php

namespace App\Http\Middleware;

use Closure;
use App\UserRole;

/**
 * CheckUserRole Middleware
 * 
 * This middleware checks if the authenticated user has the specified role.
 */
class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }
        
        $userRole = UserRole::find($user->role_id);
        
        if (!$userRole || $userRole->name != $role) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You do not have the required role.'
            ], 403);
        }
        
        return $next($request);
    }
}
