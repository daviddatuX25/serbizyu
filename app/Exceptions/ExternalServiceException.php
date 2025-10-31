<?php

namespace App\Exceptions;

class ExternalServiceException extends DomainException
{
    public function __construct(string $message = "External service error.")
    {
        parent::__construct($message, 502); // 502 Bad Gateway
    }
}
