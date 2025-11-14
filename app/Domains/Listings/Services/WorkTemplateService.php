<?php

namespace App\Domains\Listings\Services;

use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Listings\Models\WorkTemplate;
use App\Domains\Listings\Models\WorkCatalog;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class WorkTemplateService
{
    public function getWorkTemplate(int $id): WorkTemplate
    {
        $workTemplate = WorkTemplate::find($id);
        if ($workTemplate == null) {
            throw new ResourceNotFoundException('Work template does not exist.');
        }
        return $workTemplate;
    }

    public function getAllWorkTemplates(): Collection
    {
        return WorkTemplate::all();
    }

    public function createWorkTemplate(array $data): WorkTemplate
    {
        return WorkTemplate::create($data);
    }

    public function createWorkTemplateFromCatalog(WorkflowTemplate $workflow, WorkCatalog $catalogItem): WorkTemplate
    {
        $lastStep = $workflow->workTemplates()->orderBy('order', 'desc')->first();

        return WorkTemplate::create([
            'workflow_template_id' => $workflow->id,
            'work_catalog_id' => $catalogItem->id,
            'name' => $catalogItem->name,
            'description' => $catalogItem->description,
            'price' => $catalogItem->price,
            'duration_minutes' => $catalogItem->duration_minutes,
            'order' => $lastStep ? $lastStep->order + 1 : 0,
        ]);
    }

    public function updateWorkTemplate(WorkTemplate $workTemplate, array $data): WorkTemplate
    {
        $workTemplate->update($data);
        return $workTemplate;
    }

    public function deleteWorkTemplate(WorkTemplate $workTemplate): void
    {
        $workTemplate->delete();
    }

    public function reorderWorkTemplates(WorkflowTemplate $workflow, array $steps): void
    {
        DB::transaction(function () use ($workflow, $steps) {
            foreach ($steps as $step) {
                $workflow->workTemplates()
                    ->where('id', $step['id'])
                    ->update(['order' => $step['order']]);
            }
        });
    }
}
