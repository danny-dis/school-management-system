<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Helpers\AppHelper;
use Illuminate\Support\Facades\Auth;

/**
 * ParentMiddleware - Ensures only parents can access parent portal
 * 
 * This middleware checks if the authenticated user has the parent role
 * before allowing access to parent portal routes.
 * 
 * @package App\Http\Middleware
 * @author Zophlic Development Team
 */
class ParentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        // Check if user has parent role
        if (session('user_role_id', 0) != AppHelper::USER_PARENTS) {
            return redirect()->route('user.dashboard')->with('error', 'You do not have permission to access the parent portal.');
        }
        
        // Check if parent has children
        $user = Auth::user();
        $hasChildren = \App\Student::where('guardian_phone_no', $user->phone_no)
            ->orWhere('father_phone_no', $user->phone_no)
            ->orWhere('mother_phone_no', $user->phone_no)
            ->exists();
            
        if (!$hasChildren) {
            return redirect()->route('user.dashboard')->with('error', 'No children found associated with your account.');
        }
        
        return $next($request);
    }
}
