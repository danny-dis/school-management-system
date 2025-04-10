<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ApiController extends Controller
{
    /**
     * Return a success JSON response.
     *
     * @param array|string $data
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success($data, string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Return an error JSON response.
     *
     * @param string $message
     * @param int $code
     * @param array|string|null $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error(string $message = null, int $code = 400, $data = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Return a JSON resource with additional data.
     *
     * @param JsonResource $resource
     * @param string $message
     * @param int $code
     * @return JsonResource
     */
    protected function respondWithResource(JsonResource $resource, string $message = null, int $code = 200): JsonResource
    {
        return $resource->additional([
            'success' => true,
            'message' => $message
        ])->response()->setStatusCode($code);
    }

    /**
     * Return a JSON collection with additional data.
     *
     * @param ResourceCollection $collection
     * @param string $message
     * @param int $code
     * @return ResourceCollection
     */
    protected function respondWithCollection(ResourceCollection $collection, string $message = null, int $code = 200): ResourceCollection
    {
        return $collection->additional([
            'success' => true,
            'message' => $message
        ])->response()->setStatusCode($code);
    }
}
