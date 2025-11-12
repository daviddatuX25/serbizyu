<?php

namespace App\Domains\Listings\Http\Controllers;

use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Listings\Services\WorkflowTemplateService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkflowTemplateController extends Controller
{
    public function __construct(private readonly WorkflowTemplateService $workflowTemplateService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workflowTemplates = $this->workflowTemplateService->getWorkflowTemplatesByCreator(Auth::id());
        return view('creator.workflows.index', compact('workflowTemplates'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $workflow = $this->workflowTemplateService->createWorkflowTemplate([
            'name' => 'Untitled Workflow',
            'creator_id' => Auth::id(),
        ]);

        return redirect()->route('creator.workflows.edit', $workflow);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkflowTemplate $workflow)
    {

        $this->authorize('update', $workflow);   
        return view('creator.workflows.builder', ['workflowTemplate' => $workflow]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WorkflowTemplate $workflow)
    {
        $this->authorize('update', $workflow);
        $this->workflowTemplateService->updateWorkflowTemplate($workflow, $request->all());
        return redirect()->route('creator.workflows.index')->with('success', 'Workflow updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkflowTemplate $workflow)
    {
        $this->authorize('delete', $workflow);
        $this->workflowTemplateService->deleteWorkflowTemplate($workflow);
        return redirect()->route('creator.workflows.index')->with('success', 'Workflow deleted successfully.');
    }

    /**
     * Duplicate the specified resource.
     */
    public function duplicate(WorkflowTemplate $workflow)
    {
        $this->authorize('duplicate', $workflow);
        $this->workflowTemplateService->duplicateWorkflowTemplate($workflow);
        return redirect()->route('creator.workflows.index')->with('success', 'Workflow duplicated successfully.');
    }
}
