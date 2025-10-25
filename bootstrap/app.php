<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
         // Handle all domain exceptions generically
        $exceptions->render(function (DomainException $e, Request $request) {
            $response = [
                'error' => class_basename($e),
                'message' => $e->getMessage(),
            ];

            // If it's validation, include field errors
            if ($e instanceof ValidationException) {
                $response['errors'] = $e->getErrors();
            }

            return response()->json($response, $e->getStatus());
        });

        // Custom report hook
        $exceptions->report(function (DomainException $e) {
            \Log::warning("DomainException: " . $e->getMessage(), [
                'exception' => $e,
            ]);
        });
    })->create();
