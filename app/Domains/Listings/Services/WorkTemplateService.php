<?php

namespace App\Domains\Listings\Services;

use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Listings\Models\WorkTemplate;
use App\Domains\Listings\Models\WorkCatalog;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;

class WorkTemplateService
{

//    get, getAll, create
    public function getWorkTemplate($id): WorkTemplate
    {
        // get a work template
        $workTemplate = WorkTemplate::find($id);
        if ($workTemplate == null) {
            throw new ResourceNotFoundException('Work template does not exist.');
        }
        return $workTemplate;
    }

    public function getAllWorkTemplates(): Collection
    {
        $workTemplates = WorkTemplate::all();

        if ($workTemplates->isEmpty()) {
            throw new ResourceNotFoundException('No work templates found.');
        }
        
        return $workTemplates;
    }

    public function createWorkTemplate(array $data): WorkTemplate
    {
        // check if work catalog exists
        $workCatalog = WorkCatalog::find($data['work_catalog_id']);
        if ($workCatalog == null) {
            throw new ResourceNotFoundException('Work catalog does not exist.');
        }

        return WorkTemplate::create($data);
    }

   
}

