<?php

namespace App\Domains\Work\Http\Controllers;

use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Services\OrderCompletionService;
use App\Domains\Work\Models\WorkInstance;
use App\Domains\Work\Models\WorkInstanceStep;
use App\Http\Controllers\Controller;
use App\Notifications\WorkStepCompleted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class WorkInstanceController extends Controller
{
    protected OrderCompletionService $orderCompletionService;

    public function __construct(OrderCompletionService $orderCompletionService)
    {
        $this->orderCompletionService = $orderCompletionService;
    }

    /**
     * Display a listing of the resource.
     * Shows work instances for both seller (they work on) and buyer (they purchased)
     */
    public function index()
    {
        $currentUserId = Auth::id();

        $workInstances = WorkInstance::whereHas('order', function ($query) use ($currentUserId) {
            $query->where(function ($q) use ($currentUserId) {
                $q->where('seller_id', $currentUserId)
                    ->orWhere('buyer_id', $currentUserId);
            });
        })->with('order')->get();

        return view('creator.work-dashboard', compact('workInstances'));
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
     * Handles both:
     * - Old route: /work-instances/{workInstance}
     * - New route: /orders/{order}/work
     */
    public function show(?Order $order = null, ?WorkInstance $workInstance = null)
    {
        // New nested route: /orders/{order}/work (order is injected)
        if ($order !== null) {
            $workInstance = $order->workInstance;
            if (! $workInstance) {
                abort(404, 'No work instance found for this order');
            }
        } elseif ($workInstance !== null) {
            // Old route: /work-instances/{workInstance} (workInstance is injected)
            // workInstance already set
        } else {
            abort(404, 'Invalid work instance request');
        }

        $this->authorize('view', $workInstance);
        $workInstance->load('workInstanceSteps.activityThread.messages');

        return view('work.show', compact('workInstance'));
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

    public function startStep(?Order $order = null, ?WorkInstance $workInstance = null, ?WorkInstanceStep $workInstanceStep = null)
    {
        // New nested route: /orders/{order}/work/steps/{step}/start
        if ($order !== null && $workInstanceStep !== null) {
            $workInstance = $order->workInstance;
        } elseif ($workInstance === null) {
            // Try to find from old route parameter
            abort(404, 'Invalid work instance request');
        }

        // Authorize using policy
        $this->authorize('completeStep', $workInstance);

        $workInstanceStep->status = 'in_progress';
        $workInstanceStep->started_at = now();
        $workInstanceStep->save();

        // Mark work instance as started if not already
        if (! $workInstance->hasStarted()) {
            $workInstance->started_at = now();
            $workInstance->status = 'in_progress';
            $workInstance->save();
        }

        return back()->with('success', 'Step started.');
    }

    public function completeStep(?Order $order = null, ?WorkInstance $workInstance = null, ?WorkInstanceStep $workInstanceStep = null)
    {
        // New nested route: /orders/{order}/work/steps/{step}/complete
        if ($order !== null && $workInstanceStep !== null) {
            $workInstance = $order->workInstance;
        } elseif ($workInstance === null) {
            // Try to find from old route parameter
            abort(404, 'Invalid work instance request');
        }

        // Authorize using policy
        $this->authorize('completeStep', $workInstance);

        $workInstanceStep->status = 'completed';
        $workInstanceStep->completed_at = now();
        $workInstanceStep->save();

        // Update parent work instance
        $workInstance->current_step_index = $workInstance->current_step_index + 1;

        $allStepsCompleted = $workInstance->workInstanceSteps()->where('status', '!=', 'completed')->doesntExist();

        if ($allStepsCompleted) {
            $workInstance->status = 'completed';
            $workInstance->completed_at = now();
            $workInstance->save();

            // Get the order
            $order = $workInstance->order;

            // Attempt to complete the order if payment is settled
            $orderCompleted = $this->orderCompletionService->attemptCompletion($order);

            if (! $orderCompleted && $order->payment_status !== 'paid') {
                return back()->with('error', 'Cannot mark work complete: Payment must be settled first. The buyer needs to complete payment before you can finalize the order.');
            }

            // Send notifications to both buyer and seller
            $notifyUsers = collect([$order->buyer, $order->seller])->unique('id');
            Notification::send($notifyUsers, new WorkStepCompleted($workInstanceStep));

            $message = $orderCompleted
                ? 'All work steps completed and payment settled! Order is now complete. Buyer can now leave a review.'
                : 'All work steps completed. Order will be marked complete once payment is settled.';

            return back()->with('success', $message);
        }

        $workInstance->save();

        // Send notifications to both buyer and seller
        $notifyUsers = collect([$workInstance->order->buyer, $workInstance->order->seller])->unique('id');
        Notification::send($notifyUsers, new WorkStepCompleted($workInstanceStep));

        return back()->with('success', 'Step completed. Notifications sent to buyer and seller.');
    }
}
