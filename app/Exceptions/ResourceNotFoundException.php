<?php

namespace App\Exceptions;

class ResourceNotFoundException extends DomainException
{
    public function __construct(string $message = "The requested resource was not found.")
    {
        parent::__construct($message, 404); // 404 Not Found
    }
}
