<?php

namespace App\Exceptions;

class BusinessRuleException extends DomainException
{
    public function __construct(string $message = "Business rule violated")
    {
        parent::__construct($message, 422);
    }
}
