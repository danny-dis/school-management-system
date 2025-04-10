<?php

namespace App\Exceptions\Api;

class NotFoundException extends ApiException
{
    /**
     * Create a new not found exception instance.
     *
     * @param string $message
     * @param mixed $data
     * @param \Throwable|null $previous
     * @return void
     */
    public function __construct(string $message = 'Resource not found', $data = null, \Throwable $previous = null)
    {
        parent::__construct($message, 404, $data, $previous);
    }
}
