<?php

namespace App\Domains\Orders\Http\Controllers;

use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Services\OrderService;
use App\Domains\Listings\Models\OpenOfferBid;
use App\Domains\Users\Models\UserReview;
use App\Domains\Listings\Models\ServiceReview;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        // Get orders where user is either buyer or seller
        $orders = Order::where('buyer_id', $user->id)
            ->orWhere('seller_id', $user->id)
            ->with('service', 'buyer', 'seller')
            ->orderByDesc('created_at')
            ->get();
        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('orders.create');
    }

    /**
     * Store a newly created resource in storage.
     * Routes based on pay_first requirement:
     * - If pay_first = true: redirect to payment
     * - If pay_first = false: create order and redirect to show page with payment reminder
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
        ]);

        $service = \App\Domains\Listings\Models\Service::findOrFail($request->service_id);

        if ($service->pay_first) {
            // Create order in pending state, redirect to payment
            $order = $this->orderService->createOrderFromService($service, Auth::user());
            return redirect()->route('payments.checkout', $order);
        }

        // No pay_first required - create order and redirect to show page
        $order = $this->orderService->createOrderFromService($service, Auth::user());
        return redirect()->route('orders.show', $order)
            ->with('info', 'Order created! Please proceed with payment to start work.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::with(['buyer', 'seller', 'service'])->findOrFail($id);
        $this->authorize('view', $order);

        // Check if order is completed and pass review eligibility data
        $authUser = Auth::user();
        $isCompleted = $order->status === 'completed';
        $canReview = $isCompleted && $authUser && ($authUser->id === $order->buyer_id || $authUser->id === $order->seller_id);

        // Check if user has already left reviews
        $hasServiceReview = false;
        $hasUserReview = false;
        $buyerHasLeftServiceReview = false;

        if ($canReview) {
            $hasServiceReview = ServiceReview::where([
                ['reviewer_id', $authUser->id],
                ['service_id', $order->service_id],
                ['order_id', $order->id],
            ])->exists();

            $revieweeId = $authUser->id === $order->buyer_id ? $order->seller_id : $order->buyer_id;
            $hasUserReview = UserReview::where([
                ['reviewer_id', $authUser->id],
                ['reviewee_id', $revieweeId],
            ])->exists();

            // For seller view, check if buyer has left a service review
            if ($authUser->id === $order->seller_id) {
                $buyerHasLeftServiceReview = ServiceReview::where([
                    ['reviewer_id', $order->buyer_id],
                    ['service_id', $order->service_id],
                    ['order_id', $order->id],
                ])->exists();
            }
        }

        return view('orders.show', compact('order', 'canReview', 'hasServiceReview', 'hasUserReview', 'buyerHasLeftServiceReview'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $order = Order::findOrFail($id);
        $this->authorize('update', $order);
        return view('orders.edit', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $order = Order::findOrFail($id);
        $this->authorize('update', $order);

        $validated = $request->validate([
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        if ($request->has('cancellation_reason')) {
            $order->cancellation_reason = $validated['cancellation_reason'];
            $order->save();
        }

        return redirect()->route('orders.show', $order)->with('success', 'Order updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Order::findOrFail($id);
        $this->authorize('delete', $order);

        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Order deleted successfully');
    }

    public function createFromBid(string $bid)
    {
        $bidModel = OpenOfferBid::findOrFail($bid);
        $order = $this->orderService->createOrderFromBid($bidModel);
        return redirect()->route('orders.show', $order);
    }

    public function cancel(string $order)
    {
        $order = Order::findOrFail($order);
        $this->authorize('delete', $order);

        $this->orderService->cancelOrder($order);
        return redirect()->route('orders.show', $order)->with('success', 'Order cancelled successfully');
    }
}
