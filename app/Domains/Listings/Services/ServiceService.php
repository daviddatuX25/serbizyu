<?php

namespace App\Domains\Listings\Services;

use App\Domains\Listings\Models\Service;
use App\Exceptions\ResourceNotFoundException;
use App\Domains\Users\Services\UserService;
use App\Domains\Listings\Services\CategoryService;
use App\Domains\Listings\Services\WorkflowTemplateService;
use App\Domains\Common\Services\AddressService;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Plank\Mediable\MediaUploader;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Symfony\Component\ErrorHandler\Debug;

class ServiceService
{
    public function __construct(
        private UserService $userService,
        private CategoryService $categoryService,
        private WorkflowTemplateService $workflowTemplateService,
        private AddressService $addressService
    ) {}

    /**
     * Create a new service with optional images
     */
   /**
     * Create a new service with optional uploaded images
     */
    public function createService(array $data, array $uploadedFiles = []): Service
    {
        Log::info('Creating service', ['data_keys' => array_keys($data)]);

        // Remove any image removal keys
        $serviceData = collect($data)->except(['images_to_remove'])->toArray();

        // Create service
        $service = Service::create($serviceData);

        // Handle uploads
        $this->handleUploadedFiles($service, $uploadedFiles);

        return $service->loadMedia('gallery');
    }

    /**
     * Update an existing service and manage images
     */
    public function updateService(Service $service, array $data, array $uploadedFiles = []): Service
    {
        // Update service info
        $service->update(collect($data)->except(['images_to_remove'])->toArray());

        // Remove images if requested
        if (!empty($data['images_to_remove'])) {
            foreach ($data['images_to_remove'] as $mediaId) {
                try {
                    $service->detachMedia($mediaId);
                    Log::info("Detached media ID: $mediaId");
                } catch (\Exception $e) {
                    Log::warning('Failed to detach media: ' . $e->getMessage());
                }
            }
        }

        // Handle newly uploaded files
        $this->handleUploadedFiles($service, $uploadedFiles);

        return $service->loadMedia('gallery');
    }

    protected function handleUploadedFiles(Service $service, array $files): void
    {
        Debugbar::info('handleUploadedFiles called', ['files_count' => count($files)]);

        $uploader = app(MediaUploader::class);

        foreach ($files as $file) {
            Debugbar::info('Processing file', ['file' => $file]);

            if ($file instanceof TemporaryUploadedFile) {
                try {
                    // Use getRealPath() directly - it's already stored by Livewire
                    $sourcePath = $file->getRealPath();
                    
                    Debugbar::info('Source path', ['path' => $sourcePath, 'exists' => file_exists($sourcePath)]);

                    $media = $uploader->fromSource($sourcePath)
                        ->toDestination('public', 'services')
                        ->upload();

                    $service->attachMedia($media, 'gallery');

                    Debugbar::info('Attached media', ['media_id' => $media->id]);

                } catch (\Exception $e) {
                    Debugbar::error('Failed to upload media: ' . $e->getMessage());
                    Log::error('Media upload failed', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            } else {
                Debugbar::warning('Skipped invalid file', ['file' => $file]);
            }
        }
}

    /**
     * Delete a service
     */
    public function deleteService(Service $service): bool
    {
        return $service->delete();
    }

    /**
     * Retrieve a single service
     */
    public function getService($id): Service
    {
        $service = Service::with(['creator', 'category', 'workflowTemplate', 'address', 'media'])->withTrashed()->find($id);

        if (!$service) {
            throw new ResourceNotFoundException('Service does not exist.');
        }

        if ($service->trashed()) {
            throw new ResourceNotFoundException('Service has been deleted.');
        }

        return $service;
    }

    /**
     * Retrieve all services
     */
    public function getAllServices(): Collection
    {
        $services = Service::withMedia()->get();

        if ($services->isEmpty()) {
            throw new ResourceNotFoundException('No services found.');
        }



        return $services;
    }

    /**
     * Paginated services with filters
     */
    public function getPaginatedServices(array $filters = [], bool $withTrashed = false)
    {
        $query = Service::with(['creator.media', 'address', 'media']);

        if ($withTrashed) {
            $query->withTrashed();
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(fn($q) => $q
                ->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
            );
        }

        if (!empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        $sortable = ['created_at', 'price', 'title'];
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = $filters['sort_direction'] ?? 'desc';

        if (in_array($sortBy, $sortable)) {
            $query->orderBy($sortBy, $sortDir);
        }

        return $query->paginate(10);
    }

    /**
     * Get services by creator
     */
    public function getServicesForCreator(int $creatorId, array $filters = [], bool $withTrashed = false)
    {
        $query = Service::where('creator_id', $creatorId)
            ->with(['creator.media', 'address', 'media']);

        if ($withTrashed) {
            $query->withTrashed();
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(fn($q) => $q
                ->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
            );
        }

        if (!empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        $sortable = ['created_at', 'price', 'title'];
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = $filters['sort_direction'] ?? 'desc';

        if (in_array($sortBy, $sortable)) {
            $query->orderBy($sortBy, $sortDir);
        }

        return $query->paginate(10);
    }

    /**
     * Get filtered services (collection)
     */
    public function getFilteredServices(array $filters = [])
    {
        $query = Service::with(['creator.media', 'address', 'media']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(fn($q) => $q
                ->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
            );
        }

        if (!empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        $sortable = ['created_at', 'price', 'title'];
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = $filters['sort_direction'] ?? 'desc';

        if (in_array($sortBy, $sortable)) {
            $query->orderBy($sortBy, $sortDir);
        }

        return $query->get();
    }

    /**
     * Helper: upload a single file to the service gallery
     */
    private function handleFileUpload(Service $service, $file): void
    {
        if ($file instanceof UploadedFile && $file->isValid()) {
            try {
                $uploader = app(MediaUploader::class); 
                $media = $uploader->fromSource($file->getRealPath())
                    ->toDestination('media', 'services')
                    ->upload();

                $service->attachMedia($media, 'gallery');
                Log::info('Attached media: ' . $file->getClientOriginalName());
            } catch (\Exception $e) {
                Log::error('Failed to upload media: ' . $e->getMessage());
            }
        } else {
            Log::warning('Invalid file skipped', ['file' => $file]);
        }
    }
}
