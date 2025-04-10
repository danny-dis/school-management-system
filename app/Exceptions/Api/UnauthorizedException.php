<?php

namespace App\Exceptions\Api;

class UnauthorizedException extends ApiException
{
    /**
     * Create a new unauthorized exception instance.
     *
     * @param string $message
     * @param mixed $data
     * @param \Throwable|null $previous
     * @return void
     */
    public function __construct(string $message = 'Unauthorized', $data = null, \Throwable $previous = null)
    {
        parent::__construct($message, 401, $data, $previous);
    }
}
