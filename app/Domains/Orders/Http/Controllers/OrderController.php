<?php

namespace App\Domains\Orders\Http\Controllers;

use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Services\OrderService;
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
        ]);

        $order = $this->orderService->createOrderFromService($request->service_id, Auth::user());

        return redirect()->route('orders.show', $order);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::with(['buyer', 'seller', 'service'])->findOrFail($id);
        $this->authorize('view', $order);
        return view('orders.show', compact('order'));
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
        $order = $this->orderService->createOrderFromBid($bid);
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
