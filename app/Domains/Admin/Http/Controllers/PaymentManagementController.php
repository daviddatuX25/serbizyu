<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Domains\Payments\Models\Payment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentManagementController extends Controller
{
    /**
     * Display a listing of payments
     */
    public function index(Request $request)
    {
        $query = Payment::query()->with(['order', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
                ->orWhere('provider_reference', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                $request->date_from,
                $request->date_to.' 23:59:59',
            ]);
        }

        $payments = $query->latest()->paginate(15);

        return view('admin.payments.index', compact('payments'));
    }

    /**
     * Display the specified payment
     */
    public function show(Payment $payment)
    {
        $payment->load(['order', 'user']);

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource
     */
    public function edit(Payment $payment)
    {
        return view('admin.payments.edit', compact('payment'));
    }

    /**
     * Update the specified payment
     */
    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'status' => 'required|string',
        ]);

        $payment->update($validated);

        return redirect()->route('admin.payments.show', $payment)
            ->with('success', 'Payment updated successfully.');
    }

    /**
     * Mark payment as paid
     */
    public function markAsPaid(Payment $payment)
    {
        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return redirect()->route('admin.payments.show', $payment)
            ->with('success', 'Payment marked as paid successfully.');
    }

    /**
     * Mark payment as failed
     */
    public function markAsFailed(Payment $payment)
    {
        $payment->update([
            'status' => 'failed',
        ]);

        return redirect()->route('admin.payments.show', $payment)
            ->with('success', 'Payment marked as failed successfully.');
    }
}
