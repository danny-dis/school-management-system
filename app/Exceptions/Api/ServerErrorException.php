<?php

namespace App\Exceptions\Api;

class ServerErrorException extends ApiException
{
    /**
     * Create a new server error exception instance.
     *
     * @param string $message
     * @param mixed $data
     * @param \Throwable|null $previous
     * @return void
     */
    public function __construct(string $message = 'Server error', $data = null, \Throwable $previous = null)
    {
        parent::__construct($message, 500, $data, $previous);
    }
}
