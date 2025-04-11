# API Middleware Documentation

This document provides an overview of the middleware used in the School Management System API.

## Overview

Middleware in Laravel provides a convenient mechanism for filtering HTTP requests entering your application. The School Management System API uses several custom middleware to handle various aspects of API requests and responses.

## Available Middleware

### ApiResponseMiddleware

This middleware standardizes the format of API responses.

```php
'api.response' => \App\Http\Middleware\ApiResponseMiddleware::class,
```

#### Features:

- Ensures all API responses follow a standard format
- Converts non-JSON responses to JSON
- Adds success/error flags to responses

### ApiRateLimiter

This middleware implements rate limiting for API requests.

```php
'api.rate.limit' => \App\Http\Middleware\ApiRateLimiter::class,
```

#### Features:

- Limits the number of requests a client can make in a given time period
- Configurable limits for different endpoints
- Adds rate limit headers to responses
- Returns appropriate error responses when limits are exceeded

#### Usage:

```php
Route::middleware('api.rate.limit:30,1')->group(function () {
    // Routes with a limit of 30 requests per minute
});
```

### ApiCache

This middleware implements caching for API responses.

```php
'api.cache' => \App\Http\Middleware\ApiCache::class,
```

#### Features:

- Caches API responses for a configurable period
- Skips caching for non-GET requests
- Skips caching for authenticated requests
- Adds cache headers to responses

#### Usage:

```php
Route::middleware('api.cache:300')->group(function () {
    // Routes with a cache TTL of 300 seconds (5 minutes)
});
```

### ApiLogger

This middleware logs API requests and responses.

```php
'api.logger' => \App\Http\Middleware\ApiLogger::class,
```

#### Features:

- Logs all API requests and responses
- Adds a unique request ID to each request
- Includes the request ID in the response headers
- Excludes sensitive data from logs

### Cors

This middleware handles Cross-Origin Resource Sharing (CORS) for API requests.

```php
'cors' => \App\Http\Middleware\Cors::class,
```

#### Features:

- Adds CORS headers to responses
- Configurable allowed origins, methods, and headers
- Handles preflight requests

### SecurityHeaders

This middleware adds security headers to API responses.

```php
'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
```

#### Features:

- Adds various security headers to responses
- Helps protect against common web vulnerabilities
- Configurable header values

## Middleware Groups

The API middleware are grouped together in the `api` middleware group:

```php
'api' => [
    'throttle:60,1',
    'bindings',
    'api.response',
    'cors',
    'security.headers',
],
```

## Custom Middleware

You can create custom middleware for specific needs:

```php
php artisan make:middleware YourMiddleware
```

Then register it in the `app/Http/Kernel.php` file:

```php
protected $routeMiddleware = [
    // ...
    'your.middleware' => \App\Http\Middleware\YourMiddleware::class,
];
```

## Middleware Order

The order of middleware is important. Middleware are executed in the order they are listed in the middleware group or in the order they are applied to routes.

## Conclusion

Middleware are a powerful tool for handling cross-cutting concerns in your API. By using middleware, you can keep your controllers focused on business logic while handling common tasks like authentication, rate limiting, and response formatting in a centralized way.
