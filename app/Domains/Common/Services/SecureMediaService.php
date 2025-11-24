<?php

namespace App\Domains\Common\Services;

use Barryvdh\Debugbar\Facades\Debugbar;
use Plank\Mediable\Media;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * SecureMediaService
 * 
 * Handles sensitive/secure media with:
 * - User ownership validation
 * - Access control checks
 * - Encryption for storage paths
 * - Audit logging
 * 
 * Used for: User verification documents, sensitive identity proofs, etc.
 * 
 * ✅ FIXED: Now uses 'verification' context for proper storage paths
 */
class SecureMediaService
{
    public function __construct(private MediaService $mediaService) {}

    /**
     * Upload secure files with ownership validation
     * 
     * @param Model $model The model to attach media to (e.g., UserVerification)
     * @param array $files Array of TemporaryUploadedFile or UploadedFile instances
     * @param string $tag Media tag for organization
     * @param ?string $description Optional description for audit logs
     * 
     * @return array ['success' => [], 'errors' => [], 'skipped' => []]
     */
    public function uploadSecureFiles(
        Model $model,
        array $files,
        string $tag = 'secure-documents',
        ?string $description = null
    ): array {
        $user = Auth::user();

        // Step 1: Validate ownership
        try {
            $this->validateModelOwnership($model, $user);
        } catch (\Exception $e) {
            Log::warning('Unauthorized secure upload attempt', [
                'user_id' => $user->id,
                'model' => class_basename($model),
                'model_id' => $model->id,
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => [],
                'errors' => [['filename' => 'All', 'error' => 'Unauthorized: ' . $e->getMessage()]],
                'skipped' => [],
            ];
        }

        // Step 2: Use MediaService for core upload with option to specify folder/context
        $result = $this->mediaService->uploadToModel(
            $model, 
            $files, 
            $tag,
            false,
            'verification'           // Don't replace existing
        );

        // Step 3: Log secure upload for audit trail
        if (!empty($result['success'])) {
            $this->logSecureUpload($model, $result['success'], $user, $description);
        }

        if (!empty($result['errors'])) {
            Debugbar::error($result['errors']);
            $this->logSecureUploadErrors($model, $result['errors'], $user);
        }

        Log::info('Secure files uploaded', [
            'model' => class_basename($model),
            'model_id' => $model->id,
            'user_id' => $user->id,
            'files_uploaded' => count($result['success']),
            'errors' => count($result['errors']),
            'tag' => $tag,
            'description' => $description,
        ]);

        return $result;
    }

    /**
     * Get encrypted URL for secure media viewing
     * 
     * Used in Blade templates to generate safe URLs
     * The URL contains encrypted media_id that's decrypted on serving
     */
    public function getSecureMediaUrl(int $mediaId): string
    {
        try {
            $encryptedPayload = Crypt::encryptString(
                json_encode(['media_id' => $mediaId])
            );

            return route('media.serve-secure', ['payload' => $encryptedPayload]);
        } catch (\Exception $e) {
            Log::error('Failed to generate secure media URL', [
                'media_id' => $mediaId,
                'error' => $e->getMessage(),
            ]);

            return '#';
        }
    }

    /**
     * Serve secure media with authorization
     * 
     * This should be called by the controller handling the route
     */
    public function serveSecureMedia(string $encryptedPayload): StreamedResponse
    {
        $user = Auth::user();

        try {
            // Decrypt the payload
            $payload = json_decode(
                Crypt::decryptString($encryptedPayload),
                true
            );

            if (!isset($payload['media_id'])) {
                abort(400, 'Invalid payload');
            }

            $mediaId = $payload['media_id'];

            // Get media
            $media = Media::findOrFail($mediaId);

            // Get the model this media is attached to
            $mediable = $media->mediables()->first();

            if (!$mediable) {
                abort(404, 'Media not found');
            }

            // Authorize access
            $this->authorizeMediaAccess($mediable->mediable, $user);

            // Log access for audit trail
            Log::info('Secure media accessed', [
                'media_id' => $mediaId,
                'user_id' => $user->id,
                'model' => class_basename($mediable->mediable),
                'model_id' => $mediable->mediable_id,
            ]);

            // Stream the file
            return $this->streamSecureMedia($media);

        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            Log::warning('Invalid secure media URL', [
                'user_id' => $user->id,
                'error' => 'Decryption failed',
            ]);
            abort(404);
        }
    }

    /**
     * Remove secure media with audit logging
     */
    public function removeSecureMedia(Model $model, int $mediaId): array
    {
        $user = Auth::user();

        try {
            // Validate ownership
            $this->validateModelOwnership($model, $user);

            // Use MediaService to remove
            $result = $this->mediaService->removeMediaFromModel($model, $mediaId);

            if ($result['success']) {
                Log::warning('Secure media removed (audit)', [
                    'model' => class_basename($model),
                    'model_id' => $model->id,
                    'media_id' => $mediaId,
                    'user_id' => $user->id,
                    'action' => 'SECURE_MEDIA_DELETED',
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Failed to remove secure media', [
                'media_id' => $mediaId,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to remove media: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Remove multiple secure media files
     */
    public function removeMultipleSecureMedia(Model $model, array $mediaIds): array
    {
        $user = Auth::user();

        try {
            $this->validateModelOwnership($model, $user);

            $result = $this->mediaService->removeMultipleMediaFromModel($model, $mediaIds);

            Log::warning('Multiple secure media removed (audit)', [
                'model' => class_basename($model),
                'model_id' => $model->id,
                'media_ids' => $mediaIds,
                'user_id' => $user->id,
                'removed_count' => count($result['success']),
            ]);

            return $result;

        } catch (\Exception $e) {
            return [
                'success' => [],
                'errors' => [
                    ['media_id' => 'all', 'error' => $e->getMessage()]
                ],
            ];
        }
    }

    /**
     * Get secure media for a model
     */
    public function getSecureMedia(Model $model, string $tag = 'secure-documents'): array
    {
        $user = Auth::user();

        try {
            // Validate ownership
            $this->validateModelOwnership($model, $user);

            // Get media from MediaService
            return $this->mediaService->getModelMedia($model, $tag);

        } catch (\Exception $e) {
            Log::warning('Unauthorized attempt to view secure media', [
                'user_id' => $user->id,
                'model' => class_basename($model),
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Validate that the authenticated user owns this model
     * 
     * Throws exception if unauthorized
     */
    private function validateModelOwnership(Model $model, $user): void
    {
        // Check common ownership patterns
        if (isset($model->user_id) && $model->user_id === $user->id) {
            return;
        }

        if (isset($model->creator_id) && $model->creator_id === $user->id) {
            return;
        }

        // Check if user owns the parent model
        if (method_exists($model, 'user') && $model->user && $model->user->id === $user->id) {
            return;
        }

        throw new \Illuminate\Auth\Access\AuthorizationException(
            'You do not own this resource'
        );
    }

    /**
     * Authorize viewing of media
     * 
     * Owner can view their own media
     * Admins can view any media
     */
    private function authorizeMediaAccess(Model $model, $user): void
    {
        // Owner can always access
        if ((isset($model->user_id) && $model->user_id === $user->id) || 
            (isset($model->creator_id) && $model->creator_id === $user->id)) {
            return;
        }

        // Admins can access
        if (method_exists($user, 'hasRole') && 
            ($user->hasRole('admin') || $user->hasRole('moderator'))) {
            return;
        }

        throw new \Illuminate\Auth\Access\AuthorizationException(
            'Unauthorized to access this media'
        );
    }

    /**
     * Stream secure media with proper headers
     */
    private function streamSecureMedia(Media $media): StreamedResponse
    {
        return response()->streamDownload(
            function () use ($media) {
                try {
                    $stream = $media->stream();
                    while ($bytes = $stream->read(1024 * 8)) {
                        echo $bytes;
                    }
                } catch (\Exception $e) {
                    Log::error('Error streaming secure media', [
                        'media_id' => $media->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            },
            $media->basename,
            [
                'Content-Type' => $media->mime_type,
                'Content-Length' => $media->size,
                'Content-Disposition' => 'inline; filename="' . $media->basename . '"',
                // Prevent caching of sensitive docs
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]
        );
    }

    /**
     * Log successful secure upload for audit trail
     */
    private function logSecureUpload(
        Model $model,
        array $uploadedFiles,
        $user,
        ?string $description = null
    ): void {
        foreach ($uploadedFiles as $file) {
            Log::info('Secure media uploaded (audit)', [
                'action' => 'SECURE_MEDIA_UPLOADED',
                'model' => class_basename($model),
                'model_id' => $model->id,
                'media_id' => $file['media_id'],
                'filename' => $file['filename'],
                'type' => $file['type'],
                'user_id' => $user->id,
                'user_email' => $user->email,
                'description' => $description,
                'timestamp' => now()->toIso8601String(),
            ]);
        }
    }

    /**
     * Log secure upload errors for audit trail
     */
    private function logSecureUploadErrors(
        Model $model,
        array $errors,
        $user
    ): void {
        foreach ($errors as $error) {
            Log::warning('Secure media upload failed (audit)', [
                'action' => 'SECURE_MEDIA_UPLOAD_FAILED',
                'model' => class_basename($model),
                'model_id' => $model->id,
                'filename' => $error['filename'],
                'error' => $error['error'],
                'user_id' => $user->id,
                'user_email' => $user->email,
                'timestamp' => now()->toIso8601String(),
            ]);
        }
    }
}