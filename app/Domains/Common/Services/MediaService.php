<?php

namespace App\Domains\Common\Services;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Plank\Mediable\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;

class MediaService
{
    protected $gate;
    protected $config;

    public function __construct(Gate $gate, Config $config)
    {
        $this->gate = $gate;
        $this->config = $config;
    }

    /**
     * Find, authorize, and stream a media file by its ID.
     *
     * @param Request $request
     * @param int $mediaId
     * @return StreamedResponse
     */
    public function streamById(Request $request, int $mediaId): StreamedResponse
    {
        $media = Media::find($mediaId);

        if (!$media) {
            Log::warning('Media not found in database for ID.', ['media_id' => $mediaId]);
            abort(404, 'Media not found.');
        }

        $mediable = DB::table('mediables')->where('media_id', $mediaId)->first();

        if (!$mediable) {
            Log::error('Could not find parent model for media.', ['media_id' => $mediaId]);
            abort(404, 'Media source not found.');
        }

        $modelClass = $mediable->mediable_type;
        $model = $modelClass::find($mediable->mediable_id);

        if (!$model) {
            Log::error('Could not find parent model instance for media.', ['media_id' => $mediaId, 'mediable_type' => $mediable->mediable_type, 'mediable_id' => $mediable->mediable_id]);
            abort(404, 'Media source not found.');
        }

        // Check privacy setting for the model from config/media-privacy.php
        $privacy = $this->config->get('media-privacy.models.' . get_class($model), $this->config->get('media-privacy.default', 'private'));

        if ($privacy === 'private') {
            // Authorize using the policy defined for the parent model.
            if ($this->gate->denies('viewMedia', $model)) {
                Log::warning('Authorization denied by parent model policy.', [
                    'user_id' => $request->user()->id,
                    'model' => get_class($model),
                    'model_id' => $model->id
                ]);
                abort(403, 'You do not have permission to view this file.');
            }
        }

        return $this->streamPrivateMedia($media);
    }

    /**
     * Stream the media to the client.
     *
     * @param  \Plank\Mediable\Media $media
     * @return StreamedResponse
     */
    private function streamPrivateMedia(Media $media): StreamedResponse
    {
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
                'Content-Disposition' => 'inline; filename="' . $media->basename . '"',
            ]
        );
    }
}