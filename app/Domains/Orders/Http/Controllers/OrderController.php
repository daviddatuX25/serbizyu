<?php

namespace App\Domains\Orders\Http\Controllers;

use App\Domains\Listings\Models\OpenOfferBid;
use App\Domains\Orders\Http\Requests\StoreOrderRequest;
use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Services\OrderService;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $orders = Order::where('buyer_id', $user->id)
                       ->orWhere('seller_id', $user->id)
                       ->with(['buyer', 'seller', 'service'])
                       ->latest()
                       ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        $bidId = $request->query('open_offer_bid_id');

        if (! $bidId) {
            return redirect()->route('dashboard')->with('error', 'No bid specified for order creation.');
        }

        $bid = OpenOfferBid::with(['openOffer.user', 'service'])->findOrFail($bidId);

        $this->authorize('create', $bid);

        return view('orders.create', compact('bid'));
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $bid = OpenOfferBid::findOrFail($request->validated('open_offer_bid_id'));

        $this->authorize('create', $bid);

        try {
            $order = $this->orderService->createOrder($bid, $request->user());
            return redirect()->route('orders.show', $order)->with('success', 'Order created successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create order: '.$e->getMessage());
        }
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        return view('orders.show', compact('order'));
    }

    public function destroy(Order $order): RedirectResponse
    {
        $this->authorize('cancel', $order);

        try {
            $order->update([
                'status' => OrderStatus::Cancelled,
                'cancelled_at' => now(),
                'cancellation_reason' => 'Cancelled by buyer.', // Default reason, can be expanded
            ]);
            return redirect()->route('orders.show', $order)->with('success', 'Order cancelled successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel order: '.$e->getMessage());
        }
    }
}
