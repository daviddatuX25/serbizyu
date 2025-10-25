<?php
namespace App\Domains\Listings\Services;

use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Listings\Services\WorkflowTemplateService;
use App\Domains\Listings\Services\CategoryService;
use App\Domains\Users\Services\UserService;

use App\Exceptions\AuthorizationException;
use App\Exceptions\BusinessRuleException;
use App\Exceptions\ResourceNotFoundException;
use Illuminate\Database\Eloquent\Collection;

class OpenOfferService
{

    public function __construct(
        private UserService $userService,
        private CategoryService $categoryService,
        private WorkflowTemplateService $workflowTemplateService
    ){}


    public function createOpenOffer($data): OpenOffer
    {
        if ($data['price'] <= 0) {
            throw new BusinessRuleException('Price must be greater than 0.');
        }

        $this->categoryService->getCategory($data['category_id']);
        
        if (!empty($data['workflow_template_id'])) {
            $workflow = $this->workflowTemplateService->getWorkflowTemplate($data['workflow_template_id']);            

            if (!$workflow->is_public && $workflow->creator() != $data['creator_id']) 
            {
                throw new AuthorizationException('Workflow does not belong to creator.');
            }
        }

        $creator = $this->userService->getUser($data['creator_id']);
        if($creator->id != $data['creator_id'])
        {
            throw new AuthorizationException('User must be a creator to create an open offer.');
        }
        return OpenOffer::create($data);
    }

    public function getOpenOffer($id): OpenOffer
    {
        // get a service
        $openOffer = OpenOffer::find($id);
        if ($openOffer == null) {
            throw new ResourceNotFoundException('Open Offer does not exist.');
        }
        
        if ($openOffer->trashed()) {
            throw new ResourceNotFoundException('Open Offer has been deleted.');
        }
        return $openOffer;
    }

    public function getAllOpenOffers(): Collection
    {
        $openOffers = OpenOffer::with('creator', 'category', 'workflow')->get();

        if ($openOffers->isEmpty())
        {
            throw new ResourceNotFoundException('No openOffers found.');
        }
        
        if ($openOffers->every->trashed()) 
        {
            throw new ResourceNotFoundException('Open offers have all been deleted.');
        }
        return $openOffers;
    }


    public function closeOpenOffer($id): OpenOffer
    {
        $openOffer = OpenOffer::find($id);
        if ($openOffer == null) {
            throw new ResourceNotFoundException('Open offer does not exist.');
        }
        
        if ($openOffer->trashed()) {
            throw new ResourceNotFoundException('Open offer has been deleted.');
        }
        if ($openOffer->is_closed == true) {
            throw new BusinessRuleException('Open offer is already closed.');
        }

        $openOffer->update(['is_closed' => true]);
        return $openOffer;
    }
    
}