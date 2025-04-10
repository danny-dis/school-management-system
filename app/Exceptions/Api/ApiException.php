<?php

namespace App\Exceptions\Api;

use Exception;
use Illuminate\Http\JsonResponse;

class ApiException extends Exception
{
    /**
     * @var bool
     */
    protected $success = false;

    /**
     * @var int
     */
    protected $statusCode = 400;

    /**
     * @var mixed
     */
    protected $data = null;

    /**
     * Create a new API exception instance.
     *
     * @param string $message
     * @param int $statusCode
     * @param mixed $data
     * @param \Throwable|null $previous
     * @return void
     */
    public function __construct(string $message = '', int $statusCode = 400, $data = null, \Throwable $previous = null)
    {
        parent::__construct($message, $statusCode, $previous);

        $this->statusCode = $statusCode;
        $this->data = $data;
    }

    /**
     * Render the exception as an HTTP response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'success' => $this->success,
            'message' => $this->getMessage(),
            'data' => $this->data
        ], $this->statusCode);
    }

    /**
     * Get the status code.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get the data.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}
