<?php

namespace App\Domains\Listings\Http\Controllers;

use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Listings\Services\WorkflowTemplateService;
use App\Domains\Listings\Services\CategoryService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkflowTemplateController extends Controller
{
    public function __construct(
        private readonly WorkflowTemplateService $workflowTemplateService,
        private readonly CategoryService $categoryService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('creator.workflows.index');
    }

    /**
     * Show the form for creating a new workflow.
     * Creates an unsaved model instance.
     */
    public function create()
    {
        $workflowTemplate = new WorkflowTemplate([
            'name' => 'Untitled Workflow',
            'description' => '',
            'creator_id' => Auth::id(),
            'is_public' => false,
        ]);
        
        $categories = $this->categoryService->listAllCategories();

        return view('creator.workflows.builder', [
            'workflowTemplate' => $workflowTemplate,
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkflowTemplate $workflow)
    {
        $this->authorize('update', $workflow);
        
        $categories = $this->categoryService->listAllCategories();

        return view('creator.workflows.builder', [
            'workflowTemplate' => $workflow,
            'categories' => $categories,
        ]);
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
        $newWorkflow = $this->workflowTemplateService->duplicateWorkflowTemplate($workflow);
        return redirect()->route('creator.workflows.edit', $newWorkflow)->with('success', 'Workflow duplicated successfully.');
    }
}