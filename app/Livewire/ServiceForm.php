<?php

namespace App\Livewire;

use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Services\ServiceService;
use Barryvdh\Debugbar\Facades\Debugbar;
use Barryvdh\Debugbar\Twig\Extension\Debug;

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
        'workflow_template_id' => 'required|exists:workflow_templates,id',
        'address_id' => 'required|exists:addresses,id',
        'pay_first' => 'boolean',
        'newFiles.*' => 'nullable|image|max:2048',
    ];

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
        $this->workflow_template_id = '';
        $this->pay_first = false;
        $this->address_id = $this->addresses->firstWhere('pivot.is_primary', true)->id ?? null;
        $this->existingImages = [];
        $this->newFiles = [];
        $this->imagesToRemove = [];

        if ($service->exists) {
            // Edit mode — overwrite defaults
            $this->title = $service->title;
            $this->description = $service->description;
            $this->price = $service->price;
            $this->category_id = $service->category_id;
            $this->workflow_template_id = $service->workflow_template_id;
            $this->pay_first = $service->pay_first ?? false;
            $this->address_id = $service->address_id;

            $this->existingImages = $service->media->map(fn($m) => [
                'id' => $m->id,
                'url' => $m->getUrl()
            ])->toArray();
        } else {
            // Create mode — set default primary address if exists
            $primary = $this->addresses->firstWhere('pivot.is_primary', true);
            $this->address_id = $primary?->id ?? null;
        }



    }

    public function save(ServiceService $serviceService)
    {
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
            'address_id' => $this->address_id, // ✅ include address
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

    public function render()
    {
        return view('livewire.service-form');
    }
}
