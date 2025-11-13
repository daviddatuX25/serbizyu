<?php

namespace App\Domains\Common\Http\Controllers;

use App\Domains\Common\Services\MediaService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaServeController extends Controller
{
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    /**
     * Handle the incoming request for media access.
     *
     * @param Request $request
     * @param string $payload
     * @return StreamedResponse
     */
    public function __invoke(Request $request, string $payload): StreamedResponse
    {
        try {
            $data = json_decode(Crypt::decryptString($payload), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Exception $e) {
            Log::error('Failed to decrypt or decode media payload.', ['error' => $e->getMessage()]);
            abort(404, 'Invalid media link.');
        }

        if (!isset($data['media_id']) || !is_numeric($data['media_id'])) {
            Log::error('Invalid media payload structure. Missing or invalid media_id.', ['payload' => $data]);
            abort(404, 'Invalid media payload.');
        }

        $mediaId = (int) $data['media_id'];

        return $this->mediaService->streamById($request, $mediaId);
    }
}