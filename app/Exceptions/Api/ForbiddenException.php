<?php

namespace App\Exceptions\Api;

class ForbiddenException extends ApiException
{
    /**
     * Create a new forbidden exception instance.
     *
     * @param string $message
     * @param mixed $data
     * @param \Throwable|null $previous
     * @return void
     */
    public function __construct(string $message = 'Forbidden', $data = null, \Throwable $previous = null)
    {
        parent::__construct($message, 403, $data, $previous);
    }
}
