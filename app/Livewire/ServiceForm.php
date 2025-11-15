<?php

namespace App\Livewire;

use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Services\ServiceService;
use Barryvdh\Debugbar\Facades\Debugbar;

class ServiceForm extends FormWithMedia
{
    public ?Service $service = null;

    public string $title = '';
    public string $description = '';
    public float|string $price = '';
    public int|string $category_id = '';
    public int|string $workflow_template_id = '';
    public bool $is_active = false;

    public $categories;
    public $workflowTemplates;

    protected $rules = [
        'title' => 'required|string|min:3|max:255',
        'description' => 'nullable|string|min:10',
        'price' => 'required|numeric|min:0.01',
        'category_id' => 'required|exists:categories,id',
        'workflow_template_id' => 'required|exists:workflow_templates,id',
        'is_active' => 'boolean',
        'newFiles.*' => 'nullable|image|max:2048',  // ADD THIS LINE
    ];

    public function mount(?Service $service = null, $categories = [], $workflowTemplates = [])
    {
        $this->service = $service;
        $this->categories = collect($categories);
        $this->workflowTemplates = collect($workflowTemplates);

        if ($service) {
            $this->title = $service->title;
            $this->description = $service->description;
            $this->price = $service->price;
            $this->category_id = $service->category_id;
            $this->workflow_template_id = $service->workflow_template_id;
            $this->is_active = $service->is_active ?? false;

            $this->existingImages = $service->media->map(fn($m) => [
                'id' => $m->id,
                'url' => $m->getUrl()
            ])->toArray();
        }
    }

    public function save(ServiceService $serviceService)
    {
        // CHECK FILES BEFORE VALIDATION
        Debugbar::info('BEFORE validate - newFiles', ['count' => count($this->newFiles), 'files' => $this->newFiles]);
        
        $this->validate();
        
        // CHECK FILES AFTER VALIDATION  
        Debugbar::info('AFTER validate - newFiles', ['count' => count($this->newFiles), 'files' => $this->newFiles]);
        
        
        // Get files BEFORE any other operations
        $uploadedFiles = $this->getUploadedFiles();
        
        // Log what we got
        Debugbar::info('Save triggered');
        Debugbar::info('newFiles property', ['newFiles' => $this->newFiles]);
        Debugbar::info('Uploaded files count', ['count' => count($uploadedFiles)]);

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'category_id' => $this->category_id,
            'workflow_template_id' => $this->workflow_template_id,
            'is_active' => $this->is_active,
            'creator_id' => auth()->id(),
            'images_to_remove' => $this->imagesToRemove,
        ];

        Debugbar::info('Data to save', $data);

        if ($this->service) {
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
