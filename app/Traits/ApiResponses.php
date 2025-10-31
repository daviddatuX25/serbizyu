<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponses
{
    protected function success(mixed $data, string $message = 'OK', int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
        ], $statusCode);
    }

    protected function errorResponse(string $message, int $statusCode): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], $statusCode);
    }

    protected function validationErrorResponse(array $errors): JsonResponse
    {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $errors,
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_UNAUTHORIZED);
    }

    protected function forbiddenResponse(string $message = 'Forbidden'): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_FORBIDDEN);
    }

    protected function notFoundResponse(string $message = 'Not Found'): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_NOT_FOUND);
    }
}