<?php

namespace App\Traits;

use App\Exceptions\Api\ForbiddenException;
use App\Exceptions\Api\NotFoundException;
use App\Exceptions\Api\ServerErrorException;
use App\Exceptions\Api\UnauthorizedException;
use App\Exceptions\Api\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponses
{
    /**
     * Return a success JSON response.
     *
     * @param mixed $data
     * @param string|null $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data = null, ?string $message = null, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Return an error JSON response.
     *
     * @param string|null $message
     * @param int $statusCode
     * @param mixed $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(?string $message = null, int $statusCode = 400, $data = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Return a JSON resource with additional data.
     *
     * @param \Illuminate\Http\Resources\Json\JsonResource $resource
     * @param string|null $message
     * @param int $statusCode
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    protected function resourceResponse(JsonResource $resource, ?string $message = null, int $statusCode = 200): JsonResource
    {
        return $resource->additional([
            'success' => true,
            'message' => $message
        ])->response()->setStatusCode($statusCode);
    }

    /**
     * Return a JSON collection with additional data.
     *
     * @param \Illuminate\Http\Resources\Json\ResourceCollection $collection
     * @param string|null $message
     * @param int $statusCode
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    protected function collectionResponse(ResourceCollection $collection, ?string $message = null, int $statusCode = 200): ResourceCollection
    {
        return $collection->additional([
            'success' => true,
            'message' => $message
        ])->response()->setStatusCode($statusCode);
    }

    /**
     * Throw a validation exception.
     *
     * @param array $errors
     * @param string|null $message
     * @throws \App\Exceptions\Api\ValidationException
     */
    protected function throwValidationException(array $errors, ?string $message = null): void
    {
        throw new ValidationException($errors, $message ?? 'Validation error');
    }

    /**
     * Throw a not found exception.
     *
     * @param string|null $message
     * @param mixed $data
     * @throws \App\Exceptions\Api\NotFoundException
     */
    protected function throwNotFoundException(?string $message = null, $data = null): void
    {
        throw new NotFoundException($message ?? 'Resource not found', $data);
    }

    /**
     * Throw an unauthorized exception.
     *
     * @param string|null $message
     * @param mixed $data
     * @throws \App\Exceptions\Api\UnauthorizedException
     */
    protected function throwUnauthorizedException(?string $message = null, $data = null): void
    {
        throw new UnauthorizedException($message ?? 'Unauthorized', $data);
    }

    /**
     * Throw a forbidden exception.
     *
     * @param string|null $message
     * @param mixed $data
     * @throws \App\Exceptions\Api\ForbiddenException
     */
    protected function throwForbiddenException(?string $message = null, $data = null): void
    {
        throw new ForbiddenException($message ?? 'Forbidden', $data);
    }

    /**
     * Throw a server error exception.
     *
     * @param string|null $message
     * @param mixed $data
     * @throws \App\Exceptions\Api\ServerErrorException
     */
    protected function throwServerErrorException(?string $message = null, $data = null): void
    {
        throw new ServerErrorException($message ?? 'Server error', $data);
    }
}
