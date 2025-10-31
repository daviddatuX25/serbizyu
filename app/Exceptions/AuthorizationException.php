<?php

namespace App\Exceptions;

class AuthorizationException extends DomainException
{
    public function __construct(string $message = "You are not authorized to perform this action.")
    {
        parent::__construct($message, 403); // 403 Forbidden
    }
}
