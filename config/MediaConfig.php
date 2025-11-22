<?php
class MediaConfig
{
    public const UPLOAD_LIMITS = [
        'images' => 2048,      // 2MB
        'documents' => 5120,   // 5MB
        'videos' => 10240,     // 10MB
        'default' => 5120,     // 5MB
    ];

    public const ALLOWED_EXTENSIONS = [
        'images' => ['jpg', 'jpeg', 'png', 'gif', 'heic'],
        'documents' => ['pdf', 'doc', 'docx', 'txt'],
        'videos' => ['mp4', 'webm', 'mov'],
        'all' => ['jpg', 'jpeg', 'png', 'gif', 'heic', 'pdf', 'mp4', 'webm', 'mov'],
    ];

    public static function getRulesForType(string $type = 'images'): array
    {
        $limit = self::UPLOAD_LIMITS[$type] ?? self::UPLOAD_LIMITS['default'];
        $extensions = implode(',', self::ALLOWED_EXTENSIONS[$type] ?? self::ALLOWED_EXTENSIONS['all']);
        
        return [
            'newFiles.*' => "file|max:{$limit}|mimetypes:{$extensions}",
        ];
    }
}