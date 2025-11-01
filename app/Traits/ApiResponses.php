<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponses
{
    protected function success(string $message = 'Success', $data = [], int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    protected function error(string $message = 'Error', array $errors = [], int $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }

    protected function respondWithToken(string $token, string $message = 'Authenticated', int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
            ],
        ], $statusCode);
    }

    protected function validationErrorResponse(array $errors, string $message = 'Validation Error', int $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY): JsonResponse
    {
        return $this->error($message, $errors, $statusCode);
    }

    protected function unauthorizedResponse(string $message = 'Unauthorized', int $statusCode = Response::HTTP_UNAUTHORIZED): JsonResponse
    {
        return $this->error($message, [], $statusCode);
    }

    protected function forbiddenResponse(string $message = 'Forbidden', int $statusCode = Response::HTTP_FORBIDDEN): JsonResponse
    {
        return $this->error($message, [], $statusCode);
    }

    protected function notFoundResponse(string $message = 'Not Found', int $statusCode = Response::HTTP_NOT_FOUND): JsonResponse
    {
        return $this->error($message, [], $statusCode);
    }

    protected function errorResponse(string $message = 'Error', int $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return $this->error($message, [], $statusCode);
    }
}