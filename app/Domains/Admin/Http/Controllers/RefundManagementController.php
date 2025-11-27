<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Domains\Payments\Models\Refund;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefundManagementController extends Controller
{
    /**
     * Display a listing of refunds
     */
    public function index(Request $request)
    {
        $query = Refund::query()->with(['order', 'requestedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('order', function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%");
            })
                ->orWhereHas('requestedBy', function ($q) use ($search) {
                    $q->where('firstname', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%");
                });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                $request->date_from,
                $request->date_to.' 23:59:59',
            ]);
        }

        $refunds = $query->latest()->paginate(15);

        return view('admin.refunds.index', compact('refunds'));
    }

    /**
     * Display the specified refund
     */
    public function show(Refund $refund)
    {
        $refund->load(['order', 'requestedBy']);

        return view('admin.refunds.show', compact('refund'));
    }

    /**
     * Show the form for editing the specified resource
     */
    public function edit(Refund $refund)
    {
        return view('admin.refunds.edit', compact('refund'));
    }

    /**
     * Approve the refund request
     */
    public function approve(Request $request, Refund $refund)
    {
        $validated = $request->validate([
            'admin_notes' => 'nullable|string',
        ]);

        $refund->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'admin_notes' => $validated['admin_notes'] ?? null,
        ]);

        return redirect()->route('admin.refunds.show', $refund)
            ->with('success', 'Refund approved successfully.');
    }

    /**
     * Reject the refund request
     */
    public function reject(Request $request, Refund $refund)
    {
        $validated = $request->validate([
            'admin_notes' => 'required|string',
        ]);

        $refund->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'admin_notes' => $validated['admin_notes'],
        ]);

        return redirect()->route('admin.refunds.show', $refund)
            ->with('success', 'Refund rejected successfully.');
    }

    /**
     * Mark refund as completed
     */
    public function complete(Refund $refund)
    {
        if ($refund->status !== 'approved') {
            return back()->with('error', 'Only approved refunds can be marked as completed.');
        }

        $refund->update(['status' => 'completed']);

        return redirect()->route('admin.refunds.show', $refund)
            ->with('success', 'Refund marked as completed successfully.');
    }
}
