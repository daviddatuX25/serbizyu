<?php

namespace App\Http\Controllers\Domains\Users\Http\Controllers;

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

    public function store(Request $request)
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

        $path_front = $request->file('id_front')->store("private/verifications/{$user->id}");
        $path_back = $request->file('id_back')->store("private/verifications/{$user->id}");

        UserVerification::updateOrCreate(
            ['user_id' => $user->id],
            [
                'id_type' => $request->id_type,
                'id_front_path' => $path_front,
                'id_back_path' => $path_back,
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
        $verification = UserVerification::where('user_id', Auth::id())->first();

        return view('verification.status', ['verification' => $verification]);
    }
}
