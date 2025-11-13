<?php

namespace App\Domains\Users\Http\Controllers\Admin;

use App\Domains\Users\Models\UserVerification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class UserVerificationController extends Controller
{
    public function index()
    {
        $verifications = UserVerification::where('status', 'pending')->with('user')->paginate(10);

        return view('admin.verifications.index', ['verifications' => $verifications]);
    }

    public function show(UserVerification $verification)
    {
        // Get media directly from the verification model
        $idFrontMedia = $verification->getMedia('verification-id-front')->first();
        $idBackMedia = $verification->getMedia('verification-id-back')->first();

        return view('admin.verifications.show', [
            'verification' => $verification,
            'idFrontMedia' => $idFrontMedia,
            'idBackMedia' => $idBackMedia,
        ]);
    }

    public function approve(UserVerification $verification)
    {
        $verification->status = 'approved';
        $verification->reviewed_at = now();
        $verification->reviewed_by = Auth::id();
        $verification->rejection_reason = null;
        $verification->save();

        $verification->user->is_verified = true;
        $verification->user->verified_at = now();
        $verification->user->save();

        // TODO: Send notification to user

        return redirect()->route('admin.verifications.index')->with('success', 'User verification approved.');
    }

    public function reject(Request $request, UserVerification $verification)
    {
        $request->validate(['rejection_reason' => 'required|string|max:500']);

        $verification->status = 'rejected';
        $verification->reviewed_at = now();
        $verification->reviewed_by = Auth::id();
        $verification->rejection_reason = $request->rejection_reason;
        $verification->save();

        // Ensure user is not marked as verified
        $verification->user->is_verified = false;
        $verification->user->save();

        // TODO: Send notification to user

        return redirect()->route('admin.verifications.index')->with('success', 'User verification rejected.');
    }


}
