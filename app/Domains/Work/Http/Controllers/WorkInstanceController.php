<?php

namespace App\Domains\Work\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Notifications\WorkStepCompleted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

use App\Domains\Work\Models\WorkInstance;
use App\Domains\Work\Models\WorkInstanceStep;

class WorkInstanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workInstances = WorkInstance::whereHas('order', function ($query) {
            $query->where('seller_id', Auth::id());
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
     */
    public function show(WorkInstance $workInstance)
    {
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

    public function startStep(WorkInstance $workInstance, WorkInstanceStep $workInstanceStep)
    {
        // Authorize using policy
        $this->authorize('completeStep', $workInstance);

        $workInstanceStep->status = 'in_progress';
        $workInstanceStep->started_at = now();
        $workInstanceStep->save();

        // Mark work instance as started if not already
        if (!$workInstance->hasStarted()) {
            $workInstance->started_at = now();
            $workInstance->status = 'in_progress';
            $workInstance->save();
        }

        return back()->with('success', 'Step started.');
    }

    public function completeStep(WorkInstance $workInstance, WorkInstanceStep $workInstanceStep)
    {
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
        }

        $workInstance->save();

        // Send notifications to both buyer and seller
        $notifyUsers = collect([$workInstance->order->buyer, $workInstance->order->seller])->unique('id');
        Notification::send($notifyUsers, new WorkStepCompleted($workInstanceStep));

        return back()->with('success', 'Step completed. Notifications sent to buyer and seller.');
    }
}
