<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiResponseMiddleware
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
        // Process the request
        $response = $next($request);

        // If the response is already a JsonResponse, return it as is
        if ($response instanceof JsonResponse) {
            return $response;
        }

        // If the request expects JSON but the response is not a JsonResponse,
        // convert it to a standard API response format
        if ($request->expectsJson() && !$response instanceof JsonResponse) {
            $statusCode = $response->getStatusCode();
            
            // For successful responses
            if ($statusCode >= 200 && $statusCode < 300) {
                return response()->success(
                    $response->getContent(),
                    'Request successful',
                    $statusCode
                );
            }
            
            // For error responses
            return response()->error(
                'Request failed',
                $statusCode,
                $response->getContent()
            );
        }

        return $response;
    }
}
