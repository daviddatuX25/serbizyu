<?php
namespace App\Domains\Listings\Services;

use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Models\Category;
use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Users\Models\User;
use App\DTO\ServiceResponseDto;
use App\Domains\Listings\Services\Contracts\ServiceInterface;   
use Illuminate\Database\Eloquent\Collection;

class ServiceService implements ServiceInterface
{
    public function createService($data): ServiceResponseDto
    {

        // validate if category, workflow, and creator is existing or is not soft deleted
        // use find() instead of firstOrFail()
        $category = Category::find($data['category_id']);
        if ($category == null) {
            return ServiceResponseDto::error('Category does not exist.');
        } else if ($category->trashed()) {
            return ServiceResponseDto::warning('Category has been deleted.');
        }

        $workflow = WorkflowTemplate::find($data['workflow_template_id']);
        if ($workflow == null) {
            return ServiceResponseDto::error('Workflow does not exist.');
        } else if ($workflow->trashed()) {
            return ServiceResponseDto::warning('Workflow has been deleted.');
        }

        $creator = User::find($data['creator_id']);
        if ($creator == null) {
            return ServiceResponseDto::error('Creator does not exist.');
        }

        // create service
        $service = Service::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'price' => $data['price'],
            'pay_first' => $data['pay_first'],
            'category_id' => $data['category_id'],
            'workflow_template_id' => $data['workflow_template_id'],
            'creator_id' => $data['creator_id'],
        ]);

        return ServiceResponseDto::success($service);
    }

    public function updateService($id, array $data): ServiceResponseDto
    {
        //
    }

    public function deleteService($id): ServiceResponseDto
    {
        //
    }

    public function getService($id): ServiceResponseDto
    {
        //
    }

    public function getAllServices(): ServiceResponseDto
    {
        //
    }

    public function getServicesBySlug($slug): ServiceResponseDto
    {
        //
    }
}