<?php

namespace App\Domains\Users\Http\Controllers;

use App\Domains\Users\Http\Requests\StoreUserVerificationRequest;
use App\Domains\Users\Models\UserVerification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Plank\Mediable\MediaUploader;

class UserVerificationController extends Controller
{
    public function create()
    {
        $existingVerification = UserVerification::where('user_id', Auth::id())->first();

        if ($existingVerification && in_array($existingVerification->status, ['pending', 'approved'])) {
            return redirect()->route('verification.status');
        }

        return view('creator.verification.submit');
    }

    public function store(StoreUserVerificationRequest $request, MediaUploader $uploader)
    {
        $user = Auth::user();

        // Prevent re-submission if already pending or approved
        $existingVerification = UserVerification::where('user_id', $user->id)->first();
        if ($existingVerification && in_array($existingVerification->status, ['pending', 'approved'])) {
            return redirect()->route('verification.status')->with('error', 'You already have a pending or approved verification request.');
        }

        // Create or update the verification record first
        $verification = UserVerification::updateOrCreate(
            ['user_id' => $user->id],
            [
                'id_type' => $request->id_type,
                'status' => 'pending',
                'rejection_reason' => null,
                'reviewed_at' => null,
                'reviewed_by' => null,
            ]
        );

        // Upload the front ID and attach to the verification record
        if ($request->hasFile('id_front')) {
            $media = $uploader->fromSource($request->file('id_front'))
                ->toDestination('local', 'verifications/'.$user->id) // Use user ID as directory
                ->upload();
            $verification->attachMedia($media, 'verification-id-front');
        }

        // Upload the back ID and attach to the verification record
        if ($request->hasFile('id_back')) {
            $media = $uploader->fromSource($request->file('id_back'))
                ->toDestination('local', 'verifications/'.$user->id) // Use user ID as directory
                ->upload();
            $verification->attachMedia($media, 'verification-id-back');
        }

        return redirect()->route('verification.status')->with('success', 'Your verification documents have been submitted successfully.');
    }

    public function status()
    {
        $user = Auth::user();
        $verification = UserVerification::where('user_id', $user->id)->first();
        $idFrontMedia = $verification ? $verification->getMedia('verification-id-front')->first() : null;
        $idBackMedia = $verification ? $verification->getMedia('verification-id-back')->first() : null;

        return view('creator.verification.status', [
            'verification' => $verification,
            'idFrontMedia' => $idFrontMedia,
            'idBackMedia' => $idBackMedia,
        ]);
    }
}
