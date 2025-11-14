<?php

namespace App\Domains\Listings\Http\Controllers;

use App\Domains\Listings\Http\Requests\StoreWorkTemplateRequest;
use App\Domains\Listings\Http\Requests\UpdateWorkTemplateRequest;
use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Listings\Models\WorkTemplate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WorkTemplateController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkTemplateRequest $request, WorkflowTemplate $workflowTemplate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkTemplateRequest $request, WorkTemplate $workTemplate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkTemplate $workTemplate)
    {
        //
    }

    /**
     * Reorder the resources in storage.
     */
    public function reorder(Request $request, WorkflowTemplate $workflowTemplate)
    {
        //
    }
}
