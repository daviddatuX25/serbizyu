<?php

namespace App\Livewire;

use App\Domains\Common\Interfaces\AddressProviderInterface;
use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Services\ServiceService;
use App\Enums\PaymentMethod;
use App\Livewire\Traits\Addressable;
use App\Livewire\Traits\WithMedia;
use App\Livewire\Traits\Workflowable;
use Livewire\Component;

class ServiceForm extends Component
{
    use Addressable;
    use WithMedia;
    use Workflowable;

    public ?Service $service = null;

    // Livewire properties for Service
    public ?string $title = '';

    public ?string $description = '';

    public float|string|null $price = null;

    public int|string|null $category_id = null;

    public ?bool $pay_first = false;

    public ?string $payment_method = 'any';

    public ?int $address_id = null;

    public $categories = [];

    public $paymentMethods = [];

    public function boot(AddressProviderInterface $addressProvider)
    {
        $this->bootAddressable($addressProvider);
    }

    protected function rules()
    {
        $rules = [
            'title' => 'required|string|min:3|max:255',
            'description' => 'nullable|string|min:10',
            'price' => 'required|numeric|min:0.01',
            'category_id' => 'required|exists:categories,id',
            'workflow_template_id' => 'required|integer|exists:workflow_templates,id',
            'address_id' => 'nullable|exists:addresses,id',
            'pay_first' => 'boolean',
            'payment_method' => 'required|in:cash,online,any',
        ];

        if ($this->showAddressModal) {
            $rules = array_merge($rules, $this->addressValidationRules());
        }

        return $rules;
    }

    public function mount(?Service $service = null, $categories = [], $addresses = [])
    {
        $this->service = $service;
        $this->categories = collect($categories);
        $this->paymentMethods = PaymentMethod::options();
        $this->mountAddressable(collect($addresses));
        $this->mountWorkflowable($service);

        if ($this->service && $this->service->exists) {
            // Edit mode – populate with existing data
            $this->title = $this->service->title;
            $this->description = $this->service->description;
            $this->price = $this->service->price;
            $this->category_id = $this->service->category_id;
            $this->pay_first = $this->service->pay_first ?? false;
            $this->payment_method = $this->service->payment_method?->value ?? 'any';
            $this->address_id = $this->service->address_id;
            $this->loadExistingMedia($this->service);
        } else {
            // Create mode – set default primary address if exists
            $primary = $this->addresses->firstWhere('pivot.is_primary', true);
            $this->address_id = $primary?->id ?? null;
            $this->payment_method = 'any';

            // Check if workflow_id is passed in request
            if (request()->has('workflow_id')) {
                $this->workflow_template_id = request('workflow_id');
            }
        }
    }

    public function save(ServiceService $serviceService)
    {
        if (empty($this->address_id) && ! $this->showAddressModal) {
            $this->addError('address_id', 'Please select an address or add a new one.');

            return;
        }

        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        }

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'category_id' => $this->category_id,
            'workflow_template_id' => $this->workflow_template_id,
            'address_id' => $this->address_id,
            'pay_first' => $this->pay_first,
            'payment_method' => $this->payment_method,
            'creator_id' => auth()->id(),
        ];

        $uploadedFiles = $this->getUploadedFiles();
        $imagesToRemove = $this->imagesToRemove;

        $data['images_to_remove'] = $imagesToRemove;

        try {
            if ($this->service && $this->service->exists) {
                $serviceService->updateService(
                    $this->service,
                    $data,
                    $uploadedFiles
                );
            } else {
                $serviceService->createService($data, $uploadedFiles);
            }

            $this->resetMediaForm();
            session()->flash('success', 'Service saved successfully!');

            return redirect()->route('creator.services.index');

        } catch (\Throwable $e) {
            $this->addError('save', 'Failed to save service: '.$e->getMessage());
        }
    }

    public function restoreImage(int $mediaId)
    {
        $this->restoreExistingImage($mediaId, $this->service);
    }

    public function render()
    {
        return view('livewire.service-form');
    }
}
