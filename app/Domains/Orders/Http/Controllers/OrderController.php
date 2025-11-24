<?php

namespace App\Domains\Orders\Http\Controllers;

use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Services\OrderService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        $orders = auth()->user()->orders()->with('service')->get();
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::with(['buyer', 'seller', 'service'])->findOrFail($id);
        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function createFromBid(string $bid)
    {
        $order = $this->orderService->createOrderFromBid($bid);
        return redirect()->route('orders.show', $order);
    }

    public function cancel(string $order)
    {
        $order = Order::findOrFail($order);
        $this->orderService->cancelOrder($order);
        return redirect()->route('orders.show', $order);
    }
}
