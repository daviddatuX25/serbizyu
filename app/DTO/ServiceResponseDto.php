<?php

namespace App\DTO;

use App\DTO\Dto;

class ServiceResponseDto implements Dto
{
    public function __construct(
        protected bool $success = true,
        protected $errors = [],
        protected $warnings = [],
        protected $data = [],
    )
    {}


    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function addErrors(array $errors): void
    {
        $this->errors = array_merge($this->errors, $errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function addWarnings(array $warnings): void
    {
        $this->warnings = array_merge($this->warnings, $warnings);
    }

    public function getWarnings(): array
    {
        return $this->warnings;
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'errors' => $this->errors,
            'warnings' => $this->warnings,
            'data' => $this->data
        ];
    }


    public function toJSON(): string
    {
        return json_encode($this->toArray());
    }

    // Static factory method
    public static function success($data = []): ServiceResponseDto
    {
        return new static(true, [], [], $data);
    }

    public static function error($errors): ServiceResponseDto
    {
        if (!is_array($errors)) {
            $errors = [$errors];
        }

        return new static(false, $errors);

    }

    public static function warning($warnings): ServiceResponseDto
    {
        if (!is_array($warnings)) {
            $warnings = [$warnings];
        }
        return new static(true, [], $warnings);
    }
    
}