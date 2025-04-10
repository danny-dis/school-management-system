<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Helpers\AppHelper;
use Illuminate\Support\Facades\Auth;

class StudentMiddleware
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
        
        // Check if user has student role
        if (session('user_role_id', 0) != AppHelper::USER_STUDENT) {
            return redirect()->route('user.dashboard')->with('error', 'You do not have permission to access the student portal.');
        }
        
        // Check if student has a student profile
        if (!Auth::user()->student) {
            return redirect()->route('user.dashboard')->with('error', 'No student profile found for your account.');
        }
        
        return $next($request);
    }
}
