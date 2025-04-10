<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Register API response macros
        $this->registerResponseMacros();
    }

    /**
     * Register response macros for consistent API responses.
     *
     * @return void
     */
    protected function registerResponseMacros()
    {
        // Success response
        Response::macro('success', function ($data = null, $message = null, $statusCode = 200) {
            return Response::json([
                'success' => true,
                'message' => $message,
                'data' => $data
            ], $statusCode);
        });

        // Error response
        Response::macro('error', function ($message = null, $statusCode = 400, $data = null) {
            return Response::json([
                'success' => false,
                'message' => $message,
                'data' => $data
            ], $statusCode);
        });

        // Validation error response
        Response::macro('validationError', function ($errors, $message = 'Validation error') {
            return Response::json([
                'success' => false,
                'message' => $message,
                'data' => ['errors' => $errors]
            ], 422);
        });

        // Not found response
        Response::macro('notFound', function ($message = 'Resource not found') {
            return Response::json([
                'success' => false,
                'message' => $message,
                'data' => null
            ], 404);
        });

        // Unauthorized response
        Response::macro('unauthorized', function ($message = 'Unauthorized') {
            return Response::json([
                'success' => false,
                'message' => $message,
                'data' => null
            ], 401);
        });

        // Forbidden response
        Response::macro('forbidden', function ($message = 'Forbidden') {
            return Response::json([
                'success' => false,
                'message' => $message,
                'data' => null
            ], 403);
        });

        // Server error response
        Response::macro('serverError', function ($message = 'Server error', $data = null) {
            return Response::json([
                'success' => false,
                'message' => $message,
                'data' => $data
            ], 500);
        });
    }
}
