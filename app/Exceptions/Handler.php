<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ApiResponses;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    use ApiResponses;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($request->wantsJson() || $request->is('api/*')) {
            return $this->handleApiException($request, $exception);
        }

        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    private function handleApiException($request, Throwable $exception)
    { 
        $exception = $this->prepareException($exception);

        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            return $this->validationErrorResponse($exception->errors());
        }

        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            return $this->unauthorizedResponse($exception->getMessage());
        }

        if ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
            return $this->forbiddenResponse($exception->getMessage());
        }

        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            return $this->notFoundResponse('The requested resource was not found.');
        }

        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
            return $this->errorResponse($exception->getMessage(), Response::HTTP_METHOD_NOT_ALLOWED);
        }

        if ($exception instanceof \App\Exceptions\DomainException) {
            return $this->errorResponse($exception->getMessage(), $exception->getCode() ?: Response::HTTP_BAD_REQUEST);
        }

        $statusCode = $this->isHttpException($exception) ? $exception->getStatusCode() : 500;
        $message = config('app.debug') ? $exception->getMessage() : 'Server Error';

        return $this->errorResponse($message, $statusCode);
    }
}
