<?php

namespace App\Exceptions;

class ValidationException extends DomainException
{
    protected array $errors;

    public function __construct(array $errors, string $message = "Validation failed")
    {
        parent::__construct($message, 422);
        $this->errors = $errors;
    }

    /**
     * Returns the validation errors.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
