<?php
use Illuminate\Support\Facades\Route;
use App\Domains\Users\Http\Controllers\ProfileController;
// Authentication routes
require __DIR__.'/auth.php';

// Home and static pages
    Route::get('/', function () {
        return view('home');
    })->name('home');

    Route::get('browse', function () {
        return view('browse');
    })->name('browse');

    Route::get('create', function () {
        return view('create');
    })->name('create');

    Route::get('about', function () {
        return view('about');
    })->name('about');

    Route::get('faq', function () {
        return view('faq');
    })->name('faq');

// Creator space
   Route::middleware(['auth'])->prefix('creator')->group(function () {
       Route::get('/', function () {
           return view('home');
       })->name('creator.dashboard');
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