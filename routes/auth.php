<?php

use App\Domains\Users\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Domains\Users\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Domains\Users\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Domains\Users\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Domains\Users\Http\Controllers\Auth\NewPasswordController;
use App\Domains\Users\Http\Controllers\Auth\PasswordController;
use App\Domains\Users\Http\Controllers\Auth\PasswordResetLinkController;
use App\Domains\Users\Http\Controllers\Auth\RegisteredUserController;
use App\Domains\Users\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('join', [RegisteredUserController::class, 'create'])
        ->name('auth.join');

    Route::post('join', [RegisteredUserController::class, 'store']);

    Route::get('signin', [AuthenticatedSessionController::class, 'create'])
        ->name('auth.signin');

    Route::post('signin', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
