<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ApiCache
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int  $ttl
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $ttl = 60)
    {
        // Skip caching for non-GET requests
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        // Skip caching for authenticated requests
        if ($request->user()) {
            return $next($request);
        }

        // Generate a cache key based on the request
        $key = $this->generateCacheKey($request);

        // Check if the response is cached
        if (Cache::has($key)) {
            $cachedResponse = Cache::get($key);
            
            // Add cache headers to the response
            return response()->json(
                $cachedResponse['content'],
                $cachedResponse['status']
            )->withHeaders([
                'X-Cache' => 'HIT',
                'X-Cache-TTL' => $ttl
            ]);
        }

        // Process the request
        $response = $next($request);

        // Cache the response if it's successful
        if ($response->getStatusCode() === Response::HTTP_OK) {
            Cache::put($key, [
                'content' => json_decode($response->getContent(), true),
                'status' => $response->getStatusCode()
            ], $ttl);
        }

        // Add cache headers to the response
        return $response->withHeaders([
            'X-Cache' => 'MISS',
            'X-Cache-TTL' => $ttl
        ]);
    }

    /**
     * Generate a cache key for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function generateCacheKey(Request $request)
    {
        return 'api:' . md5($request->fullUrl());
    }
}
