<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ForceJsonResponse;

use App\Domains\Listings\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AddressController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('addresses')->name('api.addresses.')->group(function () {
    Route::get('/regions', [AddressController::class, 'regions'])->name('regions');
    Route::get('/regions/{regionCode}/provinces', [AddressController::class, 'provinces'])->name('provinces');
    Route::get('/provinces/{provinceCode}/cities', [AddressController::class, 'cities'])->name('cities');
    Route::get('/cities/{cityCode}/barangays', [AddressController::class, 'barangays'])->name('barangays');
});

// Messaging API
Route::middleware('auth:sanctum')->prefix('messages')->name('api.messages.')->group(function () {
    Route::get('conversations', [\App\Domains\Messaging\Http\Controllers\MessageController::class, 'conversations'])->name('conversations');
    Route::get('{user}/history', [\App\Domains\Messaging\Http\Controllers\MessageController::class, 'history'])->name('history');
    Route::post('{user}', [\App\Domains\Messaging\Http\Controllers\MessageController::class, 'store'])->name('store');
    Route::put('{message}/read', [\App\Domains\Messaging\Http\Controllers\MessageController::class, 'markRead'])->name('read');
    Route::get('unread/count', [\App\Domains\Messaging\Http\Controllers\MessageController::class, 'unreadCount'])->name('unread');
});

// User Reviews API
Route::middleware('auth:sanctum')->prefix('reviews/users')->name('api.user-reviews.')->group(function () {
    Route::post('/', [\App\Domains\Users\Http\Controllers\ReviewController::class, 'store'])->name('store');
    Route::get('{review}', [\App\Domains\Users\Http\Controllers\ReviewController::class, 'show'])->name('show');
    Route::put('{review}', [\App\Domains\Users\Http\Controllers\ReviewController::class, 'update'])->name('update');
    Route::delete('{review}', [\App\Domains\Users\Http\Controllers\ReviewController::class, 'destroy'])->name('destroy');
    Route::get('user/{user}/received', [\App\Domains\Users\Http\Controllers\ReviewController::class, 'getUserReviews'])->name('received');
    Route::get('user/{user}/written', [\App\Domains\Users\Http\Controllers\ReviewController::class, 'getUserReviewsWritten'])->name('written');
    Route::get('user/{user}/stats', [\App\Domains\Users\Http\Controllers\ReviewController::class, 'getUserStats'])->name('stats');
});

// Service Reviews API
Route::middleware('auth:sanctum')->prefix('reviews/services')->name('api.service-reviews.')->group(function () {
    Route::get('service/{service}', [\App\Domains\Listings\Http\Controllers\ReviewController::class, 'index'])->name('index');
    Route::post('/', [\App\Domains\Listings\Http\Controllers\ReviewController::class, 'store'])->name('store');
    Route::get('{review}', [\App\Domains\Listings\Http\Controllers\ReviewController::class, 'show'])->name('show');
    Route::put('{review}', [\App\Domains\Listings\Http\Controllers\ReviewController::class, 'update'])->name('update');
    Route::delete('{review}', [\App\Domains\Listings\Http\Controllers\ReviewController::class, 'destroy'])->name('destroy');
    Route::get('service/{service}/stats', [\App\Domains\Listings\Http\Controllers\ReviewController::class, 'getServiceStats'])->name('stats');
    Route::post('{review}/helpful', [\App\Domains\Listings\Http\Controllers\ReviewController::class, 'markHelpful'])->name('helpful');
});


/*
Route::middleware(['api', ForceJsonResponse::class])->group(function () {
    Route::middleware('auth')->get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::apiResource('categories', CategoryController::class);
});
*/
