<?php

namespace App\Domains\Listings\Services;

use App\Domains\Listings\Models\Service;
use App\Config\MediaConfig;
use Plank\Mediable\MediaUploader;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Facades\Log;

class ServiceService
{
    protected MediaConfig $mediaConfig;
    protected MediaUploader $mediaUploader;

    public function __construct()
    {
        $this->mediaConfig = new MediaConfig();
        $this->mediaUploader = app(MediaUploader::class);
    }

    /**
     * Create a new service with optional images
     */
    public function createService(array $data, array $uploadedFiles = []): Service
    {
        Log::info('Creating service', ['data_keys' => array_keys($data)]);

        // Remove media-related keys from service data
        $serviceData = collect($data)->except(['images_to_remove'])->toArray();

        // Create service
        $service = Service::create($serviceData);

        // Handle uploads - uses MediaConfig for validation
        $this->handleUploadedFiles($service, $uploadedFiles, [], 'gallery');

        return $service->load('media');
    }

    /**
     * Update an existing service and manage images
     */
    public function updateService(Service $service, array $data, array $uploadedFiles = []): Service
    {
        // Update service info (NOT media)
        $serviceData = collect($data)->except(['images_to_remove'])->toArray();
        $service->update($serviceData);

        // Extract images to remove list
        $imagesToRemove = $data['images_to_remove'] ?? [];

        // Handle media: removes only explicitly requested, adds new, keeps rest
        $this->handleUploadedFiles($service, $uploadedFiles, $imagesToRemove, 'gallery');

        return $service->load('media');
    }

    /**
     * Handle media uploads using MediaConfig
     * 
     * @param Service $service The service model
     * @param array $files Newly uploaded files
     * @param array $imagesToRemove Media IDs to detach
     * @param string $tag Media tag (e.g., 'gallery')
     */
    protected function handleUploadedFiles(
        Service $service,
        array $files,
        array $imagesToRemove = [],
        string $tag = 'gallery'
    ): void {
        // STEP 1: Remove only explicitly requested media
        if (!empty($imagesToRemove)) {
            foreach ($imagesToRemove as $mediaId) {
                try {
                    $service->detachMedia($mediaId);
                    Log::info("Detached media ID: $mediaId from service {$service->id}");
                } catch (\Exception $e) {
                    Log::warning('Failed to detach media: ' . $e->getMessage());
                }
            }
        }

        // STEP 2: Upload and attach NEW files
        if (empty($files)) {
            return;
        }

        foreach ($files as $file) {
            if (!$file instanceof TemporaryUploadedFile) {
                continue;
            }

            try {
                // Detect media type from file
                $mediaType = $this->detectMediaType($file);

                // Get limit from MediaConfig
                $maxSizeKb = $this->mediaConfig->getUploadLimit($mediaType);
                $fileSizeKb = $file->getSize() / 1024;

                // Validate against MediaConfig limits
                if ($fileSizeKb > $maxSizeKb) {
                    Log::warning("File exceeds MediaConfig limit", [
                        'filename' => $file->getClientOriginalName(),
                        'size_kb' => $fileSizeKb,
                        'limit_kb' => $maxSizeKb,
                        'type' => $mediaType,
                    ]);
                    continue;
                }

                $sourcePath = $file->getRealPath();

                // Upload using MediaConfig destination
                $destination = $this->mediaConfig->getDestination($mediaType);
                
                $media = $this->mediaUploader->fromSource($sourcePath)
                    ->toDestination('public', $destination)
                    ->upload();

                // Attach to service - preserves existing media
                $service->attachMedia($media, $tag);

                Log::info('Attached new media', [
                    'media_id' => $media->id,
                    'service_id' => $service->id,
                    'type' => $mediaType,
                    'destination' => $destination,
                ]);

            } catch (\Exception $e) {
                Log::error('Media upload failed: ' . $e->getMessage(), [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
    }

    /**
     * Detect media type from uploaded file
     * Uses MIME type to determine category
     */
    private function detectMediaType(TemporaryUploadedFile $file): string
    {
        $mimeType = $file->getMimeType();

        return match (true) {
            str_starts_with($mimeType, 'image/') => 'images',
            str_starts_with($mimeType, 'video/') => 'videos',
            str_starts_with($mimeType, 'audio/') => 'audio',
            str_contains($mimeType, 'pdf') => 'documents',
            str_contains($mimeType, 'word') || str_contains($mimeType, 'document') => 'documents',
            default => 'documents',
        };
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
        $service = Service::with(['creator', 'category', 'workflowTemplate', 'address', 'media'])->find($id);

        if (!$service) {
            throw new \App\Exceptions\ResourceNotFoundException('Service does not exist.');
        }

        if ($service->trashed()) {
            throw new \App\Exceptions\ResourceNotFoundException('Service has been deleted.');
        }

        return $service;
    }

    /**
     * Retrieve all services
     */
    public function getAllServices()
    {
        $services = Service::withMedia()->get();

        if ($services->isEmpty()) {
            throw new \App\Exceptions\ResourceNotFoundException('No services found.');
        }

        if ($services->every->trashed()) {
            throw new \App\Exceptions\ResourceNotFoundException('Services have all been deleted.');
        }

        return $services;
    }

    /**
     * Paginated services with filters
     */
    public function getPaginatedServices(array $filters = [])
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

        return $query->paginate(10);
    }

    /**
     * Get services by creator
     */
    public function getServicesForCreator(int $creatorId, array $filters = [])
    {
        $query = Service::where('creator_id', $creatorId)
            ->with(['creator.media', 'address', 'media']);

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
}