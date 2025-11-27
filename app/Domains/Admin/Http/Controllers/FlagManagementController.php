<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Domains\Admin\Services\FlagActionService;
use App\Domains\Listings\Models\Flag;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FlagManagementController extends Controller
{
    public function __construct(private FlagActionService $flagActionService) {}

    /**
     * Display a listing of flags
     */
    public function index(Request $request)
    {
        $query = Flag::query()->with(['user', 'admin', 'flaggable']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('reason', 'like', "%{$search}%")
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('firstname', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%");
                });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                $request->date_from,
                $request->date_to.' 23:59:59',
            ]);
        }

        $flags = $query->latest()->paginate(15);

        return view('admin.flags.index', compact('flags'));
    }

    /**
     * Display the specified flag
     */
    public function show(Flag $flag)
    {
        $flag->load(['user', 'admin', 'flaggable']);

        return view('admin.flags.show', compact('flag'));
    }

    /**
     * Store a newly created flag in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'flaggable_type' => 'required|string',
            'flaggable_id' => 'required|integer',
            'category' => 'required|string|in:spam,inappropriate,fraud,misleading_info,copyright_violation,other',
            'reason' => 'required|string|max:500',
        ]);

        Flag::create([
            'flaggable_type' => $validated['flaggable_type'],
            'flaggable_id' => $validated['flaggable_id'],
            'user_id' => Auth::id(),
            'category' => $validated['category'],
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Flag submitted successfully for review.');
    }

    /**
     * Show the form for editing the specified resource
     */
    public function edit(Flag $flag)
    {
        return view('admin.flags.edit', compact('flag'));
    }

    /**
     * Approve the flag (take action)
     */
    public function approve(Request $request, Flag $flag)
    {
        $validated = $request->validate([
            'admin_notes' => 'required|string',
        ]);

        $flag->update([
            'status' => 'approved',
            'admin_id' => Auth::id(),
            'admin_notes' => $validated['admin_notes'],
            'reviewed_at' => now(),
        ]);

        // Execute automated actions
        $results = $this->flagActionService->handleFlagApproval($flag);

        $message = 'Flag approved.';
        if ($results['content_suspended']) {
            $message .= ' Content suspended.';
        }
        if ($results['refund_initiated']) {
            $message .= ' Refund initiated.';
        }
        if ($results['escalation_triggered']) {
            $message .= ' Creator escalation triggered.';
        }
        if ($results['error']) {
            $message .= " Warning: {$results['error']}";
        }

        return redirect()->route('admin.flags.show', $flag)
            ->with('success', $message);
    }

    /**
     * Reject the flag (false report)
     */
    public function reject(Request $request, Flag $flag)
    {
        $validated = $request->validate([
            'admin_notes' => 'required|string',
        ]);

        $flag->update([
            'status' => 'rejected',
            'admin_id' => Auth::id(),
            'admin_notes' => $validated['admin_notes'],
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.flags.show', $flag)
            ->with('success', 'Flag rejected.');
    }

    /**
     * Resolve the flag (already handled)
     */
    public function resolve(Request $request, Flag $flag)
    {
        $validated = $request->validate([
            'admin_notes' => 'nullable|string',
        ]);

        $flag->update([
            'status' => 'resolved',
            'admin_id' => Auth::id(),
            'admin_notes' => $validated['admin_notes'] ?? null,
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.flags.show', $flag)
            ->with('success', 'Flag marked as resolved.');
    }
}
