<?php

namespace App\Support;

/**
 * Centralized media configuration
 *
 * Used by ServiceService, OpenOfferService, and Livewire forms
 * to ensure consistent file size limits and storage destinations
 */
class MediaConfig
{
    /**
     * Upload size limits in KB for each media type
     */
    public const UPLOAD_LIMITS = [
        'images' => 5120,      // 5MB
        'documents' => 5120,   // 5MB
        'videos' => 10240,     // 10MB
        'audio' => 5120,       // 5MB
        'default' => 5120,     // 5MB default
    ];

    /**
     * Storage destinations for each media type
     */
    public const STORAGE_DESTINATIONS = [
        'images' => 'services/images',
        'documents' => 'services/documents',
        'videos' => 'services/videos',
        'audio' => 'services/audio',
        'default' => 'services',
    ];

    /**
     * Allowed file extensions by type
     */
    public const ALLOWED_EXTENSIONS = [
        'images' => ['jpg', 'jpeg', 'png', 'gif', 'heic', 'webp'],
        'documents' => ['pdf', 'doc', 'docx', 'txt', 'xls', 'xlsx'],
        'videos' => ['mp4', 'webm', 'mov', 'avi', 'mkv'],
        'audio' => ['mp3', 'wav', 'ogg', 'aac', 'm4a'],
    ];

    /**
     * Get upload limit in KB for a media type
     */
    public function getUploadLimit(string $type): int
    {
        return self::UPLOAD_LIMITS[$type] ?? self::UPLOAD_LIMITS['default'];
    }

    /**
     * Get storage destination for a media type
     */
    public function getDestination(string $type): string
    {
        return self::STORAGE_DESTINATIONS[$type] ?? self::STORAGE_DESTINATIONS['default'];
    }

    /**
     * Get allowed extensions for a media type
     */
    public function getAllowedExtensions(string $type): array
    {
        return self::ALLOWED_EXTENSIONS[$type] ?? [];
    }

    /**
     * Check if file extension is allowed
     */
    public function isExtensionAllowed(string $extension, string $type): bool
    {
        $allowed = $this->getAllowedExtensions($type);

        return in_array(strtolower($extension), $allowed);
    }

    /**
     * Generate validation rule for a media type
     *
     * @param  string  $type  Media type (images, videos, documents, audio)
     * @param  bool  $nullable  Allow null values
     * @return string Validation rule
     */
    public function getValidationRule(string $type, bool $nullable = false): string
    {
        $limitKb = $this->getUploadLimit($type);
        $extensions = implode(',', $this->getAllowedExtensions($type));

        $rule = "file|max:{$limitKb}|mimes:{$extensions}";

        if ($nullable) {
            $rule = "nullable|{$rule}";
        }

        return $rule;
    }

    /**
     * Get human-readable file size limit
     */
    public function getUploadLimitDisplay(string $type): string
    {
        $kb = $this->getUploadLimit($type);
        $mb = $kb / 1024;

        return $mb >= 1 ? intval($mb).'MB' : "{$kb}KB";
    }

    /**
     * Get validation error messages for a media type
     */
    public function getValidationMessages(string $type, string $fieldName = 'file'): array
    {
        $limitDisplay = $this->getUploadLimitDisplay($type);
        $extensions = implode(', ', $this->getAllowedExtensions($type));

        return [
            "{$fieldName}.required" => "A {$type} file is required.",
            "{$fieldName}.file" => "The {$fieldName} must be a valid file.",
            "{$fieldName}.max" => "The {$fieldName} must not exceed {$limitDisplay}.",
            "{$fieldName}.mimes" => "The {$fieldName} must be one of: {$extensions}.",
            "{$fieldName}.*.required" => "All {$type} files are required.",
            "{$fieldName}.*.file" => "Each {$fieldName} must be a valid file.",
            "{$fieldName}.*.max" => "Each {$fieldName} must not exceed {$limitDisplay}.",
            "{$fieldName}.*.mimes" => "Each {$fieldName} must be one of: {$extensions}.",
        ];
    }

    /**
     * Get image validation rule for Livewire
     * Specific for images only (used in media uploader)
     */
    public function getImageValidationRule(bool $multiple = false): string
    {
        $limitKb = $this->getUploadLimit('images');

        if ($multiple) {
            return "image|max:{$limitKb}";
        }

        return "image|max:{$limitKb}";
    }

    /**
     * Get all allowed MIME types for a media type
     */
    public function getAllowedMimeTypes(string $type): array
    {
        return match ($type) {
            'images' => [
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/heic',
                'image/webp',
            ],
            'videos' => [
                'video/mp4',
                'video/webm',
                'video/quicktime',
                'video/x-msvideo',
            ],
            'audio' => [
                'audio/mpeg',
                'audio/wav',
                'audio/ogg',
                'audio/aac',
                'audio/mp4',
            ],
            'documents' => [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/plain',
            ],
            default => [],
        };
    }
}
