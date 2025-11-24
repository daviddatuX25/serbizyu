<?php

namespace App\Domains\Messaging\Http\Controllers;

use App\Domains\Messaging\Models\Message;
use App\Domains\Messaging\Models\MessageThread;
use App\Domains\Messaging\Models\MessageAttachment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * Create a new message thread.
     */
    public function createThread(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'parent_type' => 'required|string',
            'parent_id' => 'required|integer',
        ]);

        $thread = MessageThread::create([
            'creator_id' => Auth::id(),
            'title' => $request->title,
            'parent_type' => $request->parent_type,
            'parent_id' => $request->parent_id,
        ]);

        return response()->json($thread, 201);
    }

    /**
     * Send a message within a thread.
     */
    public function sendMessage(Request $request, MessageThread $thread)
    {
        $request->validate([
            'content' => 'required|string',
            'attachments.*' => 'file|max:10240', // Max 10MB per file
        ]);

        $message = DB::transaction(function () use ($request, $thread) {
            $message = $thread->messages()->create([
                'sender_id' => Auth::id(),
                'content' => $request->content,
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('message_attachments', 'public');
                    $message->attachments()->create([
                        'file_path' => $path,
                        'file_type' => $file->getMimeType(),
                    ]);
                }
            }
            return $message;
        });

        // TODO: Broadcast MessageSent event
        // event(new MessageSent($message));

        return response()->json($message->load('attachments'), 201);
    }

    /**
     * Mark messages in a thread as read for the authenticated user.
     */
    public function markAsRead(MessageThread $thread)
    {
        $thread->messages()
            ->where('read_at', null)
            ->where('sender_id', '!=', Auth::id())
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'Messages marked as read']);
    }

    /**
     * List messages for a given thread with pagination.
     */
    public function listMessages(MessageThread $thread)
    {
        $messages = $thread->messages()->with('sender', 'attachments')->latest()->paginate(20);
        return response()->json($messages);
    }

    /**
     * Display a specific message thread.
     */
    public function show(MessageThread $thread)
    {
        return view('messages.show', compact('thread'));
    }
}
