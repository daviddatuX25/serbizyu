<?php

namespace App\Domains\Work\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
            $query->where('seller_id', auth()->id());
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
        $workInstance->load('workInstanceSteps');
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
        // Logic to start a work instance step
    }

    public function completeStep(WorkInstance $workInstance, WorkInstanceStep $workInstanceStep)
    {
        // Logic to complete a work instance step
    }
}
