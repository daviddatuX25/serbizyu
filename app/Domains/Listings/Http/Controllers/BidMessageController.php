<?php

namespace App\Domains\Listings\Http\Controllers;

use App\Domains\Listings\Models\OpenOfferBid;
use App\Domains\Messaging\Models\MessageThread;
use App\Domains\Messaging\Models\Message;
use App\Events\BidMessageSent;
use App\Http\Controllers\Controller;
use App\Notifications\BidMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class BidMessageController extends Controller
{
    /**
     * Get or create a message thread for a bid.
     */
    public function getOrCreateThread(OpenOfferBid $bid)
    {
        $this->authorize('viewMessageThread', $bid);

        $thread = MessageThread::where('parent_type', OpenOfferBid::class)
            ->where('parent_id', $bid->id)
            ->first();

        if (!$thread) {
            $thread = MessageThread::create([
                'creator_id' => Auth::id(),
                'title' => "Bid Discussion - {$bid->openOffer->title}",
                'parent_type' => OpenOfferBid::class,
                'parent_id' => $bid->id,
            ]);
        }

        return response()->json($thread);
    }

    /**
     * Get all messages for a bid.
     */
    public function index(OpenOfferBid $bid)
    {
        $this->authorize('viewMessageThread', $bid);

        $thread = MessageThread::where('parent_type', OpenOfferBid::class)
            ->where('parent_id', $bid->id)
            ->firstOrFail();

        $messages = $thread->messages()
            ->with('sender', 'attachments')
            ->latest()
            ->paginate(50);

        return response()->json([
            'thread' => $thread,
            'messages' => $messages,
            'bid' => $bid->load('bidder', 'openOffer'),
        ]);
    }

    /**
     * Send a message in a bid thread.
     */
    public function store(Request $request, OpenOfferBid $bid)
    {
        $this->authorize('sendMessage', $bid);

        $request->validate([
            'content' => 'required|string|max:5000',
            'attachments.*' => 'file|max:10240',
        ]);

        // Check if bid is in a state where messaging is allowed
        if ($bid->status === \App\Enums\BidStatus::REJECTED) {
            return response()->json([
                'message' => 'Cannot message a rejected bid.',
            ], 403);
        }

        $thread = MessageThread::where('parent_type', OpenOfferBid::class)
            ->where('parent_id', $bid->id)
            ->firstOrFail();

        $message = DB::transaction(function () use ($request, $thread) {
            $message = $thread->messages()->create([
                'sender_id' => Auth::id(),
                'content' => $request->input('content'),
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

        event(new BidMessageSent($message, $bid));

        // Determine recipient (opposite party in bid conversation)
        $recipientId = Auth::id() === $bid->bidder_id ? $bid->openOffer->creator_id : $bid->bidder_id;
        $recipient = Auth::id() === $bid->bidder_id ? $bid->openOffer->creator : $bid->bidder;

        Notification::send($recipient, new BidMessageNotification($message, $bid));

        return response()->json($message->load('attachments', 'sender'), 201);
    }

    /**
     * Mark messages as read for the authenticated user.
     */
    public function markAsRead(OpenOfferBid $bid)
    {
        $this->authorize('viewMessageThread', $bid);

        $thread = MessageThread::where('parent_type', OpenOfferBid::class)
            ->where('parent_id', $bid->id)
            ->firstOrFail();

        $thread->messages()
            ->where('read_at', null)
            ->where('sender_id', '!=', Auth::id())
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'Messages marked as read']);
    }
}
