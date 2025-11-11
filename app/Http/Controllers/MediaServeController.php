<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Plank\Mediable\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaServeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $encryptedPath): StreamedResponse
    {
        try {
            $path = Crypt::decryptString($encryptedPath);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(404);
        }

        $media = Media::where('disk', 'local')->where('directory', dirname($path))->where('filename', basename($path))->firstOrFail();

        // Ensure the user is authorized to view this media
        // For now, we'll assume if it's a verification document, only the owner or admin can view it.
        // This needs to be expanded with proper authorization logic (e.g., policies).
        if ($media->getDisk() === 'local' && $media->getDiskPath() === $path) {
            // Example: Check if the authenticated user is the owner of the media's associated model
            // Or if the user has an 'admin' role.
            // For now, we'll just serve it if it's a local file and the path matches.
            // TODO: Implement proper authorization here.
        } else {
            abort(403, 'Unauthorized to access this media.');
        }

        return response()->streamDownload(
            function () use ($media) {
                $stream = $media->stream();
                while ($bytes = $stream->read(1024)) {
                    echo $bytes;
                }
            },
            $media->basename,
            [
                'Content-Type' => $media->mime_type,
                'Content-Length' => $media->size,
                'Content-Disposition' => 'inline; filename="' . $media->basename . '"', // Display in browser
            ]
        );
    }
}
