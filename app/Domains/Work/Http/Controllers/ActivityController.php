<?php

namespace App\Domains\Work\Http\Controllers;

use App\Domains\Work\Models\ActivityMessage;
use App\Domains\Work\Models\ActivityAttachment;
use App\Domains\Work\Models\ActivityThread;
use App\Notifications\ActivityMessageCreated;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'attachments.*' => 'file|max:10240', // Max 10MB per file
        ]);

        $activityMessage = ActivityMessage::create([
            'activity_thread_id' => $request->activity_thread_id, // Assuming this is passed
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('activity_attachments', 'public'); // Placeholder for storage logic
                ActivityAttachment::create([
                    'activity_message_id' => $activityMessage->id,
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                ]);
            }
        }

        // Send notification
        $activityThread = ActivityThread::find($request->activity_thread_id);
        if ($activityThread) {
            $workInstance = $activityThread->workInstanceStep->workInstance;
            $order = $workInstance->order;

            $recipients = collect([$order->buyer, $order->seller])->unique('id');
            Notification::send($recipients, new ActivityMessageCreated($activityMessage));
        }

        return back()->with('success', 'Activity message created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'content' => 'required|string',
            'attachments.*' => 'file|max:10240', // Max 10MB per file
        ]);

        $activityMessage = ActivityMessage::findOrFail($id);
        $activityMessage->update([
            'content' => $request->content,
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('activity_attachments', 'public'); // Placeholder for storage logic
                ActivityAttachment::create([
                    'activity_message_id' => $activityMessage->id,
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                ]);
            }
        }

        return back()->with('success', 'Activity message updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $activityMessage = ActivityMessage::findOrFail($id);

        // Authorization: Only message creator can delete
        if ($activityMessage->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to delete this message.');
        }

        // Delete attachments
        if ($activityMessage->attachments()->exists()) {
            foreach ($activityMessage->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
                $attachment->delete();
            }
        }

        $activityMessage->delete();

        return back()->with('success', 'Message deleted successfully.');
    }
}
