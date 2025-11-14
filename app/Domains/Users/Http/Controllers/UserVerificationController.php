<?php

namespace App\Domains\Users\Http\Controllers;

use App\Domains\Users\Models\UserVerification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserVerificationController extends Controller
{
    public function create()
    {
        $existingVerification = UserVerification::where('user_id', Auth::id())->first();

        if ($existingVerification && in_array($existingVerification->status, ['pending', 'approved'])) {
            return redirect()->route('verification.status');
        }

        return view('verification.submit');
    }

    public function store(Request $request, \Plank\Mediable\MediaUploader $uploader)
    {
        $request->validate([
            'id_type' => ['required', 'string', 'in:national_id,drivers_license,passport'],
            'id_front' => ['required', 'image', 'max:2048'],
            'id_back' => ['required', 'image', 'max:2048'],
        ]);

        $user = Auth::user();

        // Prevent re-submission if already pending or approved
        $existingVerification = UserVerification::where('user_id', $user->id)->first();
        if ($existingVerification && in_array($existingVerification->status, ['pending', 'approved'])) {
             return redirect()->route('verification.status')->with('error', 'You already have a pending or approved verification request.');
        }

        // Upload the front ID
        if ($request->hasFile('id_front')) {
            $media = $uploader->fromSource($request->file('id_front'))
                ->toDestination('local', "verifications/{$user->id}") // Private disk
                ->upload();
            $user->syncMedia($media, 'verification-id-front');
        }

        // Upload the back ID
        if ($request->hasFile('id_back')) {
            $media = $uploader->fromSource($request->file('id_back'))
                ->toDestination('local', "verifications/{$user->id}") // Private disk
                ->upload();
            $user->syncMedia($media, 'verification-id-back');
        }

        UserVerification::updateOrCreate(
            ['user_id' => $user->id],
            [
                'id_type' => $request->id_type,
                'status' => 'pending',
                'rejection_reason' => null,
                'reviewed_at' => null,
                'reviewed_by' => null,
            ]
        );

        return redirect()->route('verification.status')->with('success', 'Your verification documents have been submitted successfully.');
    }

    public function status()
    {
        $user = Auth::user();
        $verification = UserVerification::where('user_id', $user->id)->first();
        $idFrontMedia = $user->getMedia('verification-id-front')->first();
        $idBackMedia = $user->getMedia('verification-id-back')->first();

        return view('verification.status', [
            'verification' => $verification,
            'idFrontMedia' => $idFrontMedia,
            'idBackMedia' => $idBackMedia,
        ]);
    }
}
