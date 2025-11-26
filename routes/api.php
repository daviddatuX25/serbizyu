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


/*
Route::middleware(['api', ForceJsonResponse::class])->group(function () {
    Route::middleware('auth')->get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('categories', CategoryController::class);
});
*/
