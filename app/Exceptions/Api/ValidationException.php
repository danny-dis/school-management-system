<?php

namespace App\Exceptions\Api;

class ValidationException extends ApiException
{
    /**
     * Create a new validation exception instance.
     *
     * @param array $errors
     * @param string $message
     * @param \Throwable|null $previous
     * @return void
     */
    public function __construct(array $errors, string $message = 'Validation error', \Throwable $previous = null)
    {
        parent::__construct($message, 422, ['errors' => $errors], $previous);
    }
}
