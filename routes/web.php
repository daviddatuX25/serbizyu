<?php
use Illuminate\Support\Facades\Route;
use App\Domains\Listings\Http\Controllers\ServiceController;
use App\Domains\Users\Http\Controllers\ProfileController;
use App\Domains\Users\Http\Controllers\CreatorDashboardController;
use App\Domains\Users\Http\Controllers\UserVerificationController;
use App\Domains\Users\Http\Controllers\Admin\UserVerificationController as AdminUserVerificationController;
use App\Domains\Admin\Http\Controllers\DashboardController;
use App\Domains\Admin\Http\Controllers\ActivityLogController;
use App\Domains\Admin\Http\Controllers\UserManagementController;
use App\Domains\Admin\Http\Controllers\ListingManagementController;
use App\Domains\Admin\Http\Controllers\SettingsController;
use App\Domains\Listings\Http\Controllers\CategoryController;
use App\Domains\Common\Http\Controllers\MediaServeController;
use App\Domains\Listings\Http\Controllers\ListingController;
use App\Domains\Listings\Http\Controllers\WorkflowTemplateController;
use App\Domains\Listings\Http\Controllers\OpenOfferController;
use App\Domains\Listings\Http\Controllers\OpenOfferBidController;
use App\Domains\Listings\Http\Controllers\PublicWorkflowController;
use App\Domains\Orders\Http\Controllers\OrderController;
use App\Domains\Work\Http\Controllers\WorkInstanceController;
use App\Domains\Work\Http\Controllers\ActivityController;
use App\Domains\Payments\Http\Controllers\PaymentController;
use App\Domains\Payments\Http\Controllers\PaymentWebhookController;
use App\Domains\Payments\Http\Controllers\DisbursementController;
use App\Domains\Payments\Http\Controllers\RefundController;
use App\Domains\Messaging\Http\Controllers\MessageController;
use App\Domains\Orders\Http\Controllers\OrderMessageController;
use App\Domains\Listings\Http\Controllers\BidMessageController;
use App\Domains\Payments\Http\Controllers\CashPaymentController;


// require or include auth.php
require __DIR__.'/auth.php';

// Messaging Routes
Route::middleware('auth')->group(function () {
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{thread}', [MessageController::class, 'sendMessage'])->name('messages.send');
    Route::put('/messages/{thread}/read', [MessageController::class, 'markAsRead'])->name('messages.read');
});

// Reviews API Routes (using web middleware for session auth)
Route::middleware('auth')->group(function () {
    // Service Reviews
    Route::prefix('api/reviews/services')->name('api.service-reviews.')->group(function () {
        Route::get('service/{service}', [\App\Domains\Listings\Http\Controllers\ReviewController::class, 'index'])->name('index');
        Route::post('/', [\App\Domains\Listings\Http\Controllers\ReviewController::class, 'store'])->name('store');
        Route::get('{review}', [\App\Domains\Listings\Http\Controllers\ReviewController::class, 'show'])->name('show');
        Route::put('{review}', [\App\Domains\Listings\Http\Controllers\ReviewController::class, 'update'])->name('update');
        Route::delete('{review}', [\App\Domains\Listings\Http\Controllers\ReviewController::class, 'destroy'])->name('destroy');
        Route::get('service/{service}/stats', [\App\Domains\Listings\Http\Controllers\ReviewController::class, 'getServiceStats'])->name('stats');
        Route::post('{review}/helpful', [\App\Domains\Listings\Http\Controllers\ReviewController::class, 'markHelpful'])->name('helpful');
    });

    // User Reviews
    Route::prefix('api/reviews/users')->name('api.user-reviews.')->group(function () {
        Route::post('/', [\App\Domains\Users\Http\Controllers\ReviewController::class, 'store'])->name('store');
        Route::get('{review}', [\App\Domains\Users\Http\Controllers\ReviewController::class, 'show'])->name('show');
        Route::put('{review}', [\App\Domains\Users\Http\Controllers\ReviewController::class, 'update'])->name('update');
        Route::delete('{review}', [\App\Domains\Users\Http\Controllers\ReviewController::class, 'destroy'])->name('destroy');
        Route::get('user/{user}/received', [\App\Domains\Users\Http\Controllers\ReviewController::class, 'getUserReviews'])->name('received');
        Route::get('user/{user}/written', [\App\Domains\Users\Http\Controllers\ReviewController::class, 'getUserReviewsWritten'])->name('written');
        Route::get('user/{user}/stats', [\App\Domains\Users\Http\Controllers\ReviewController::class, 'getUserStats'])->name('stats');
    });
});

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

// Public-facing open offer page
Route::get('/openoffers/{openoffer}', [OpenOfferController::class, 'show'])->name('openoffers.show');

// Public Workflow Browsing
Route::get('/workflows', [PublicWorkflowController::class, 'index'])->name('workflows');

// Creator space
Route::middleware(['auth'])->prefix('creator')->name('creator.')->group(function () {
    Route::get('/', [CreatorDashboardController::class, 'index'])->name('dashboard');

    // Service Management
    Route::resource('services', ServiceController::class);
    Route::get('services/{service}/manage', [ServiceController::class, 'manage'])->name('services.manage');

    // Category Management
    Route::resource('categories', CategoryController::class);

    // Open Offer Management
    Route::resource('openoffers', OpenOfferController::class)->except(['show']);
    Route::post('openoffers/{openoffer}/close', [OpenOfferController::class, 'close'])->name('openoffers.close');
    Route::post('openoffers/{openoffer}/renew', [OpenOfferController::class, 'renew'])->name('openoffers.renew');

    // Bidding Management for Open Offer Owners
    Route::prefix('openoffers/{openoffer}')->name('openoffers.')->group(function () {
        Route::resource('bids', OpenOfferBidController::class)->only([
            'index', 'store', 'edit', 'update', 'destroy'
        ]);
        Route::post('bids/{bid}/accept', [OpenOfferBidController::class, 'accept'])->name('bids.accept');
        Route::post('bids/{bid}/reject', [OpenOfferBidController::class, 'reject'])->name('bids.reject');
    });

    // Bid Messaging Routes (accessible by bidder and offer creator)
    Route::prefix('bids')->name('bids.')->group(function () {
        Route::prefix('{bid}/messages')->name('messages.')->group(function () {
            Route::get('/', [BidMessageController::class, 'index'])->name('index');
            Route::post('/', [BidMessageController::class, 'store'])->name('store');
            Route::get('/thread', [BidMessageController::class, 'getOrCreateThread'])->name('thread');
            Route::post('/read', [BidMessageController::class, 'markAsRead'])->name('markAsRead');
        });
    });

    // Workflow Management
    Route::get('workflows', [WorkflowTemplateController::class, 'index'])->name('workflows.index');
    Route::get('workflows/create', [WorkflowTemplateController::class, 'create'])->name('workflows.create');
    Route::get('workflows/{workflow}/edit', [WorkflowTemplateController::class, 'edit'])->name('workflows.edit');
    Route::patch('workflows/{workflow}', [WorkflowTemplateController::class, 'update'])->name('workflows.update');
    Route::delete('workflows/{workflow}', [WorkflowTemplateController::class, 'destroy'])->name('workflows.destroy');
    Route::post('workflows/{workflow}/duplicate', [WorkflowTemplateController::class, 'duplicate'])->name('workflows.duplicate');

    // Note: Work Dashboard integrated into main dashboard and order views (Phase 3)
    // Old route: /creator/work-dashboard (deprecated - work now accessed via /orders/{order}/work)
});

// Authenticated user actions (e.g., bookmarking)
Route::middleware(['auth'])->group(function () {
    // Workflow Bookmarking
    Route::post('/workflows/{workflowTemplate}/bookmark', [PublicWorkflowController::class, 'bookmark'])->name('workflows.bookmark');
    Route::delete('/workflows/{workflowTemplate}/bookmark', [PublicWorkflowController::class, 'unbookmark'])->name('workflows.unbookmark');

    // Service Checkout
    Route::post('/services/{service}/checkout', [ServiceController::class, 'checkout'])->name('services.checkout');

    // Order Management
    Route::resource('orders', \App\Domains\Orders\Http\Controllers\OrderController::class)->only(['index', 'show']);
});

// User Verification
Route::middleware(['auth'])->prefix('verification')->name('verification.')->group(function () {
    Route::get('/submit', [UserVerificationController::class, 'create'])->name('create');
    Route::post('/', [UserVerificationController::class, 'store'])->name('store');
    Route::get('/status', [UserVerificationController::class, 'status'])->name('status');
});

// Public Profile - viewable by anyone
Route::get('/profile/{user}', [ProfileController::class, 'show'])
    ->name('profile.show')
    ->where('user', '[0-9]+');

// Profile editor - edit own profile
Route::middleware(['auth'])->prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/verifications', [AdminUserVerificationController::class, 'index'])->name('verifications.index');
    Route::get('/verifications/{verification}', [AdminUserVerificationController::class, 'show'])->name('verifications.show');
    Route::post('/verifications/{verification}/approve', [AdminUserVerificationController::class, 'approve'])->name('verifications.approve');
    Route::post('/verifications/{verification}/reject', [AdminUserVerificationController::class, 'reject'])->name('verifications.reject');
    Route::resource('users', UserManagementController::class)->except(['create', 'store']);
    Route::resource('listings', ListingManagementController::class)->except(['create', 'store']);
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
});

Route::get('/media/serve/{payload}', [MediaServeController::class, '__invoke'])
    ->middleware('auth')
    ->name('media.serve');


Route::middleware(['auth'])->group(function () {
    Route::get('/messages/threads/{thread}', [App\Domains\Messaging\Http\Controllers\MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/threads', [App\Domains\Messaging\Http\Controllers\MessageController::class, 'createThread'])->name('messages.createThread');
    Route::post('/messages/threads/{thread}/messages', [App\Domains\Messaging\Http\Controllers\MessageController::class, 'sendMessage'])->name('messages.sendMessage');
    Route::post('/messages/threads/{thread}/read', [App\Domains\Messaging\Http\Controllers\MessageController::class, 'markAsRead'])->name('messages.markAsRead');
    Route::get('/messages/threads/{thread}/messages', [App\Domains\Messaging\Http\Controllers\MessageController::class, 'listMessages'])->name('messages.listMessages');
});

Route::middleware(['auth'])->prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');//
    Route::get('/create', [OrderController::class, 'create'])->name('create');
    Route::post('/', [OrderController::class, 'store'])->name('store');
    Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('edit');
    Route::put('/{order}', [OrderController::class, 'update'])->name('update');
    Route::delete('/{order}', [OrderController::class, 'destroy'])->name('destroy');
    Route::post('/from-bid/{bid}', [OrderController::class, 'createFromBid'])->name('fromBid');
    Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');

    // Order Messaging
    Route::prefix('{order}/messages')->name('messages.')->group(function () {
        Route::get('/', [OrderMessageController::class, 'index'])->name('index');
        Route::post('/', [OrderMessageController::class, 'store'])->name('store');
        Route::get('/thread', [OrderMessageController::class, 'getOrCreateThread'])->name('thread');
        Route::post('/read', [OrderMessageController::class, 'markAsRead'])->name('markAsRead');
    });
});

Route::middleware(['auth'])->prefix('payments')->name('payments.')->group(function () {
    Route::get('/checkout/{order}', [PaymentController::class, 'checkout'])->name('checkout');
    Route::post('/pay/{order}', [PaymentController::class, 'pay'])->name('pay');
    Route::get('/success', [PaymentController::class, 'success'])->name('success');
    Route::get('/failed', [PaymentController::class, 'failed'])->name('failed');

    // Cash Payment Handshake Routes
    Route::get('/cash/handshake', [PaymentController::class, 'cashHandshake'])->name('cash.handshake');
    Route::post('/cash/buyer-claimed', [PaymentController::class, 'buyerClaimedPayment'])->name('cash.buyer-claimed');
    Route::post('/cash/seller-confirmed', [PaymentController::class, 'sellerConfirmedPayment'])->name('cash.seller-confirmed');
    Route::post('/cash/seller-rejected', [PaymentController::class, 'sellerRejectedPayment'])->name('cash.seller-rejected');
    Route::get('/cash/waiting', [PaymentController::class, 'waitingForSeller'])->name('cash.wait-seller');
    Route::get('/cash/disputed', [PaymentController::class, 'paymentDisputed'])->name('cash.disputed');
});

// Work Instance Management - NEW HIERARCHICAL ROUTES (Phase 2: Order-Work Integration)
// Work is now nested under Orders for proper hierarchy and context
Route::middleware(['auth'])->prefix('orders/{order}/work')->name('orders.work.')->group(function () {
    Route::get('/', [WorkInstanceController::class, 'show'])->name('show');
    Route::post('/steps/{workInstanceStep}/start', [WorkInstanceController::class, 'startStep'])->name('steps.start');
    Route::post('/steps/{workInstanceStep}/complete', [WorkInstanceController::class, 'completeStep'])->name('steps.complete');
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
    Route::post('/steps/{workInstanceStep}/activities', [ActivityController::class, 'store'])->name('activities.store');
});

// Work Instance Management - OLD ROUTES (DEPRECATED but kept for backward compatibility)
// These will eventually be removed in Phase 3 after full migration
Route::middleware(['auth'])->prefix('work-instances')->name('work-instances.')->group(function () {
    Route::get('/{workInstance}', [WorkInstanceController::class, 'show'])->name('show');
    Route::post('/{workInstance}/steps/{workInstanceStep}/start', [WorkInstanceController::class, 'startStep'])->name('steps.start');
    Route::post('/{workInstance}/steps/{workInstanceStep}/complete', [WorkInstanceController::class, 'completeStep'])->name('steps.complete');
    Route::resource('/{workInstance}/steps/{workInstanceStep}/activities', ActivityController::class);
});

// Earnings & Disbursements
Route::middleware(['auth'])->prefix('earnings')->name('earnings.')->group(function () {
    Route::get('/', [DisbursementController::class, 'index'])->name('index');
    Route::get('/disbursement/{disbursement}', [DisbursementController::class, 'show'])->name('show');
    Route::post('/disbursement/{disbursement}/request', [DisbursementController::class, 'request'])->name('request');
});

// Refunds
Route::middleware(['auth'])->prefix('refunds')->name('refunds.')->group(function () {
    Route::get('/request/{order}', [RefundController::class, 'create'])->name('create');
    Route::post('/request/{order}', [RefundController::class, 'store'])->name('store');
    Route::get('/{refund}', [RefundController::class, 'show'])->name('show');
    Route::post('/{refund}/approve', [RefundController::class, 'approve'])->name('approve');
    Route::post('/{refund}/reject', [RefundController::class, 'reject'])->name('reject');
    Route::post('/{refund}/process', [RefundController::class, 'process'])->name('process');
});

Route::post('/payments/webhook', [PaymentWebhookController::class, 'handle'])->name('payments.webhook');

// Logout authenitcatedsessioncontroller destroy
