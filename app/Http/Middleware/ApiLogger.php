<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApiLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Generate a unique request ID
        $requestId = (string) Str::uuid();
        
        // Add the request ID to the request
        $request->headers->set('X-Request-ID', $requestId);
        
        // Log the request
        $this->logRequest($request, $requestId);
        
        // Process the request
        $response = $next($request);
        
        // Add the request ID to the response
        $response->headers->set('X-Request-ID', $requestId);
        
        // Log the response
        $this->logResponse($request, $response, $requestId);
        
        return $response;
    }
    
    /**
     * Log the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $requestId
     * @return void
     */
    protected function logRequest(Request $request, $requestId)
    {
        // Get the request data
        $method = $request->method();
        $url = $request->fullUrl();
        $ip = $request->ip();
        $userAgent = $request->header('User-Agent');
        
        // Get the authenticated user ID if available
        $userId = $request->user() ? $request->user()->id : 'guest';
        
        // Get the request body, but exclude sensitive data
        $body = $request->except(['password', 'password_confirmation', 'current_password', 'new_password', 'new_password_confirmation']);
        
        // Log the request
        Log::channel('api')->info("API Request [{$requestId}]", [
            'method' => $method,
            'url' => $url,
            'ip' => $ip,
            'user_agent' => $userAgent,
            'user_id' => $userId,
            'body' => $body
        ]);
    }
    
    /**
     * Log the response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @param  string  $requestId
     * @return void
     */
    protected function logResponse(Request $request, $response, $requestId)
    {
        // Get the response data
        $statusCode = $response->getStatusCode();
        $content = json_decode($response->getContent(), true);
        
        // Log the response
        Log::channel('api')->info("API Response [{$requestId}]", [
            'status_code' => $statusCode,
            'content' => $content
        ]);
    }
}
