<?php
namespace App\Domains\Listings\Services;

use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Listings\Services\WorkflowTemplateService;
use App\Domains\Listings\Services\CategoryService;
use App\Domains\Users\Services\UserService;
use App\Domains\Common\Services\AddressService;

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


        /**
         * Creates a new OpenOffer.
         *
         * @param array $data The data to be used when creating the OpenOffer.
         * @return OpenOffer The newly created OpenOffer.
         * @throws BusinessRuleException If the price is less than or equal to 0.
         * @throws AuthorizationException If the workflow does not belong to the creator or the user is not a creator.
         * @throws ResourceNotFoundException If the category or workflow template does not exist or has been deleted.
         */
    public function createOpenOffer($data): OpenOffer
    {
        if ($data['price'] <= 0) {
            throw new BusinessRuleException('Price must be greater than 0.');
        }

        $this->categoryService->getCategory($data['category_id']);
        
        if (!empty($data['workflow_template_id'])) {
            $workflow = $this->workflowTemplateService->getWorkflowTemplate($data['workflow_template_id']);            

            if (!$workflow->is_public && $workflow->creator_id != $data['creator_id']) 
            {
                throw new AuthorizationException('Workflow does not belong to creator.');
            }
        }

        // set address if not set then get fro mthe address of the user
        $creator = $this->userService->getUser($data['creator_id']);
        if ($creator == null) {
            throw new ResourceNotFoundException('Creator does not exist.');
        }
        if ($creator->trashed()) {
            throw new ResourceNotFoundException('Creator has been deleted.');
        }

        if ($data['address_id']) {
            $address = app(AddressService::class)->getAddress($data['address_id']);
            if ($address == null) {
                throw new ResourceNotFoundException('Address does not exist.');
            }
        } else {
            $address = $creator->addresses()->where('is_primary', true)->first();
            if ($address == null) {
                throw new ResourceNotFoundException('Creator does not have a primary address.');
            }
            $data['address_id'] = $address->id;
        }

        return OpenOffer::create($data);
    }

    /**
     * Retrieves an OpenOffer by its ID.
     *
     * @param int $id The ID of the OpenOffer to retrieve.
     * @return OpenOffer The retrieved OpenOffer.
     * @throws ResourceNotFoundException If the OpenOffer does not exist or has been deleted.
     */
    public function getOpenOffer($id): OpenOffer
    {
        // get an open offer
        $openOffer = OpenOffer::find($id)->with('creator', 'category', 'workflowTemplate.workTemplates', 'address', 'images');
        if ($openOffer == null) {
            throw new ResourceNotFoundException('Open Offer does not exist.');
        }
        
        if ($openOffer->trashed()) {
            throw new ResourceNotFoundException('Open Offer has been deleted.');
        }

        return $openOffer;
    }


/**
 * Retrieves all OpenOffers.
 *
 * @return Collection A collection of all OpenOffers.
 * @throws ResourceNotFoundException If no openOffers are found or if all openOffers have been deleted.
 */
    public function getAllOpenOffers(): Collection
    {
        $openOffers = OpenOffer::with('creator', 'category', 'workflowTemplate.workTemplates', 'address', 'thumbnail')->get();

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

    public function getPaginatedOpenOffers(array $filters = [])
    {
        // TODO: Implement filtering logic similar to ServiceService
        return OpenOffer::with('creator', 'category', 'address')
            ->latest()
            ->paginate(10); // Using a different pagination count to test merging
    }

    /**
     * Close an open offer by updating its is_closed field to true.
     *
     * @throws ResourceNotFoundException if the open offer does not exist.
     * @throws ResourceNotFoundException if the open offer has been deleted.
     * @throws BusinessRuleException if the open offer is already closed.
     *
     * @param int $id The id of the open offer to close.
     * @return OpenOffer The closed open offer.
     */

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