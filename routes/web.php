<?php
use Illuminate\Support\Facades\Route;
use App\Domains\Listings\Http\Controllers\ServiceController;
use App\Domains\Users\Http\Controllers\ProfileController;
use App\Domains\Users\Http\Controllers\UserVerificationController;
use App\Domains\Users\Http\Controllers\Admin\UserVerificationController as AdminUserVerificationController;
use App\Domains\Listings\Http\Controllers\CategoryController;

use App\Domains\Listings\Http\Controllers\ListingController;

// Authentication routes
require __DIR__.'/auth.php';

// Home and static pages
    Route::get('/', function () {
        return view('home');
    })->name('home');

    Route::get('browse', [ListingController::class, 'index'])->name('browse');

    Route::get('create', function () {
        return view('create');
    })->name('create');

    Route::get('about', function () {
        return view('about');
    })->name('about');

    Route::get('faq', function () {
        return view('faq');
    })->name('faq');

// Public-facing service page
Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');

// Creator space
    Route::prefix('creator')->name('creator.')->group(function () {
       Route::get('/', function () {
           return view('home');
       })->name('dashboard');
       Route::resource('services', ServiceController::class);
   });
// User Verification
Route::middleware(['auth'])->prefix('verification')->name('verification.')->group(function () {
    Route::get('/submit', [UserVerificationController::class, 'create'])->name('create');
    Route::post('/', [UserVerificationController::class, 'store'])->name('store');
    Route::get('/status', [UserVerificationController::class, 'status'])->name('status');
});

// Profile editor
   Route::middleware(['auth'])->prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/verifications', [AdminUserVerificationController::class, 'index'])->name('verifications.index');
    Route::get('/verifications/{verification}', [AdminUserVerificationController::class, 'show'])->name('verifications.show');
    Route::post('/verifications/{verification}/approve', [AdminUserVerificationController::class, 'approve'])->name('verifications.approve');
    Route::post('/verifications/{verification}/reject', [AdminUserVerificationController::class, 'reject'])->name('verifications.reject');
    Route::get('/verifications/image/{path}', [AdminUserVerificationController::class, 'serveImage'])->name('verifications.image')->where('path', '.*');
    Route::get('/media/serve/{encryptedPath}', \App\Http\Controllers\MediaServeController::class)->name('media.serve');
});