<?php

namespace App\Exceptions;

class ConflictException extends DomainException
{
    public function __construct(string $message = "Conflict with existing resource.")
    {
        parent::__construct($message, 409); // 409 Conflict
    }
}
