<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Domains\Orders\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderManagementController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index(Request $request)
    {
        $query = Order::query()->with(['buyer', 'seller', 'service']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('id', 'like', "%{$search}%")
                ->orWhereHas('buyer', function ($q) use ($search) {
                    $q->where('firstname', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%");
                })
                ->orWhereHas('service', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                $request->date_from,
                $request->date_to.' 23:59:59',
            ]);
        }

        // Sort by
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $orders = $query->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        $order->load(['buyer', 'seller', 'service', 'workInstance', 'payment']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource
     */
    public function edit(Order $order)
    {
        return view('admin.orders.edit', compact('order'));
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|string',
        ]);

        $order->update($validated);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order updated successfully.');
    }

    /**
     * Cancel an order
     */
    public function cancel(Order $order)
    {
        $order->update(['status' => 'cancelled']);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order cancelled successfully.');
    }
}
