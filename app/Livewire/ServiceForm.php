<?php

namespace App\Livewire;

use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Services\ServiceService;
use Barryvdh\Debugbar\Facades\Debugbar;
use Barryvdh\Debugbar\Twig\Extension\Debug;
use Livewire\Attributes\On;

class ServiceForm extends FormWithMedia
{
    public ?Service $service = null;

    // Livewire properties
    public ?string $title = null;
    public ?string $description = null;
    public float|string|null $price = null;
    public int|string|null $category_id = null;
    public int|string|null $workflow_template_id = null;
    public ?bool $pay_first = null;
    public ?int $address_id = null;

    public $categories;
    public $workflowTemplates;
    public $addresses;

    protected $rules = [
        'title' => 'required|string|min:3|max:255',
        'description' => 'nullable|string|min:10',
        'price' => 'required|numeric|min:0.01',
        'category_id' => 'required|exists:categories,id',
        'workflow_template_id' => 'required|integer|exists:workflow_templates,id',
        'address_id' => 'required|exists:addresses,id',
        'pay_first' => 'boolean',
        'newFiles.*' => 'nullable|image|max:2048',
    ];

    public function updatedWorkflowTemplateId($value)
    {
        Debugbar::info('updatedWorkflowTemplateId called', [
            'value' => $value,
            'type' => gettype($value)
        ]);
        
        // Cast to integer if it's a numeric string
        if (is_string($value) && is_numeric($value)) {
            $this->workflow_template_id = (int) $value;
            Debugbar::info('Casted to integer', ['new_value' => $this->workflow_template_id]);
        }
    }

    public function mount(
        ?Service $service = null,
        $categories = [],
        $workflowTemplates = [],
        $addresses = []
    ) {
        $this->service = $service;
        $this->categories = collect($categories);
        $this->workflowTemplates = collect($workflowTemplates);
        $this->addresses = collect($addresses)->map(function ($address) {
            $address->is_primary = $address->pivot->is_primary ?? false;
            return $address;
        });

        Debugbar::info('Addresses', ['addresses' => $this->addresses]);

        // Default values
        $this->title = '';
        $this->description = '';
        $this->price = '';
        $this->category_id = '';
        $this->workflow_template_id = null; // Changed from ''
        $this->pay_first = false;
        $this->address_id = $this->addresses->firstWhere('pivot.is_primary', true)->id ?? null;

        if ($service->exists) {
            // Edit mode — overwrite defaults
            $this->title = $service->title;
            $this->description = $service->description;
            $this->price = $service->price;
            $this->category_id = $service->category_id;
            $this->workflow_template_id = $service->workflow_template_id;
            $this->pay_first = $service->pay_first ?? false;
            $this->address_id = $service->address_id;

            $this->loadExistingMedia($this->service);
        } else {
            // Create mode — set default primary address if exists
            $primary = $this->addresses->firstWhere('pivot.is_primary', true);
            $this->address_id = $primary?->id ?? null;
        }
        
        Debugbar::info('Mount completed', [
            'service_exists' => $service->exists,
            'workflow_template_id' => $this->workflow_template_id,
            'workflowTemplates_count' => $this->workflowTemplates->count(),
            'workflowTemplates_ids' => $this->workflowTemplates->pluck('id')->toArray()
        ]);
    }

    public function save(ServiceService $serviceService)
    {
        Debugbar::info('=== SAVE METHOD CALLED ===');
        Debugbar::info('workflow_template_id value and type', [
            'value' => $this->workflow_template_id,
            'type' => gettype($this->workflow_template_id),
            'empty' => empty($this->workflow_template_id)
        ]);
        
        Debugbar::info('BEFORE validate - newFiles', ['count' => count($this->newFiles)]);

        try {
            $this->validate();
            Debugbar::info('Validation passed');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Debugbar::error('Validation failed', $e->errors());
            throw $e; // rethrow so Livewire handles displaying errors
        }

        Debugbar::info('AFTER validate - newFiles', ['count' => count($this->newFiles)]);

        $uploadedFiles = $this->getUploadedFiles();

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'category_id' => $this->category_id,
            'workflow_template_id' => $this->workflow_template_id,
            'address_id' => $this->address_id,
            'pay_first' => $this->pay_first,
            'creator_id' => auth()->id(),
            'images_to_remove' => $this->imagesToRemove,
        ];

        Debugbar::info('Data to save', $data);

        if ($this->service->exists) {
            $serviceService->updateService($this->service, $data, $uploadedFiles);
        } else {
            $serviceService->createService($data, $uploadedFiles);
        }

        session()->flash('success', 'Service saved successfully!');
        return redirect()->route('creator.services.index');
    }

     public function testLivewire()
    {
        Debugbar::info('TEST METHOD CALLED - Livewire is working!');
        session()->flash('message', 'Livewire is working!');
    }

    public function render()
    {
        return view('livewire.service-form');
    }
}