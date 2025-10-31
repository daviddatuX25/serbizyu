<?php

namespace App\Domains\Listings\Services;

use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Listings\Models\WorkTemplate;
use App\Domains\Listings\Models\WorkCatalog;
use App\Domains\Users\Services\UserService;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\AuthorizationException;
use App\Exceptions\BusinessRuleException;
use Illuminate\Database\Eloquent\Collection;

class WorkCatalogService
{
    public function __construct(
        private CategoryService $categoryService,
        private WorkflowTemplateService $workflowTemplateService
    )
    {

    }
    public function createWorkCatalog(array $data): WorkCatalog
    {
        // check if already exists
        $existingCatalog = WorkCatalog::where('name', $data['name'])->first();
        if ($existingCatalog) {
            throw new BusinessRuleException('Work catalog already exists.');
        }
        return WorkCatalog::create($data);
    }

    public function getWorkCatalog($id): WorkCatalog
    {
        // get a work catalog
        $workCatalog = WorkCatalog::find($id);
        if ($workCatalog == null) {
            throw new ResourceNotFoundException('Work catalog does not exist.');
        }
        return $workCatalog;
    }

    public function getAllWorkCatalogs(): Collection
    {
        $workCatalogs = WorkCatalog::all();

        if ($workCatalogs->isEmpty()) {
            throw new ResourceNotFoundException('No work catalogs found.');
        }
        
        return $workCatalogs;
    }
}   


?>