<?php
use Illuminate\Support\Facades\Route;
use App\Domains\Listings\Http\Controllers\ServiceController;
use App\Domains\Users\Http\Controllers\ProfileController;
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
    Route::prefix('creator')->group(function () {
       Route::get('/', function () {
           return view('home');
       })->name('dashboard');
   })->middleware(['auth']);
   
// Profile editor
   Route::middleware(['auth'])->prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

// Category Management (Admin/Creator only)
Route::middleware(['auth'])->prefix('creator')->group(function () {
    Route::get('categories', [CategoryController::class, 'index'])->name('creator.categories.index');
    Route::post('categories', [CategoryController::class, 'store'])->name('creator.categories.store');
    Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('creator.categories.edit');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->name('creator.categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('creator.categories.destroy');
    // Restore soft-deleted category
    Route::patch('categories/{category}/restore', [CategoryController::class, 'restore'])->name('creator.categories.restore');
});

// Service Management (Creator only)
Route::middleware(['auth'])->prefix('creator')->group(function () {
    Route::resource('services', ServiceController::class)->names([
        'index' => 'creator.services.index',
        'create' => 'creator.services.create',
        'store' => 'creator.services.store',
        'show' => 'creator.services.show',
        'edit' => 'creator.services.edit',
        'update' => 'creator.services.update',
        'destroy' => 'creator.services.destroy',
    ]);
});