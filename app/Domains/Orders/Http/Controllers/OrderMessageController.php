<?php

namespace App\Domains\Orders\Http\Controllers;

use App\Domains\Orders\Models\Order;
use App\Domains\Messaging\Models\MessageThread;
use App\Domains\Messaging\Models\Message;
use App\Events\OrderMessageSent;
use App\Notifications\OrderMessageNotification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class OrderMessageController extends Controller
{
    /**
     * Get or create a message thread for an order.
     */
    public function getOrCreateThread(Order $order)
    {
        $this->authorize('viewMessageThread', $order);

        $thread = MessageThread::where('parent_type', Order::class)
            ->where('parent_id', $order->id)
            ->first();

        if (!$thread) {
            $thread = MessageThread::create([
                'creator_id' => Auth::id(),
                'title' => "Order #{$order->id} - Discussion",
                'parent_type' => Order::class,
                'parent_id' => $order->id,
            ]);
        }

        return response()->json($thread);
    }

    /**
     * Get all messages for an order.
     */
    public function index(Order $order)
    {
        $this->authorize('viewMessageThread', $order);

        $thread = MessageThread::where('parent_type', Order::class)
            ->where('parent_id', $order->id)
            ->firstOrFail();

        $messages = $thread->messages()
            ->with('sender', 'attachments')
            ->latest()
            ->paginate(50);

        return response()->json([
            'thread' => $thread,
            'messages' => $messages,
        ]);
    }

    /**
     * Send a message in an order thread.
     */
    public function store(Request $request, Order $order)
    {
        $this->authorize('sendMessage', $order);

        $request->validate([
            'content' => 'required|string|max:5000',
            'attachments.*' => 'file|max:10240',
        ]);

        // Don't allow messaging if order is cancelled
        if ($order->status === 'cancelled') {
            return response()->json([
                'message' => 'Cannot message a cancelled order.',
            ], 403);
        }

        $thread = MessageThread::where('parent_type', Order::class)
            ->where('parent_id', $order->id)
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

        // Dispatch broadcast event
        event(new OrderMessageSent($message, $order));

        // Send notification to the other party
        $recipientId = Auth::id() === $order->buyer_id ? $order->seller_id : $order->buyer_id;
        $recipient = $order->buyer_id === $recipientId ? $order->buyer : $order->seller;
        Notification::send($recipient, new OrderMessageNotification($message, $order));

        return response()->json($message->load('attachments', 'sender'), 201);
    }

    /**
     * Mark messages as read for the authenticated user.
     */
    public function markAsRead(Order $order)
    {
        $this->authorize('viewMessageThread', $order);

        $thread = MessageThread::where('parent_type', Order::class)
            ->where('parent_id', $order->id)
            ->firstOrFail();

        $thread->messages()
            ->where('read_at', null)
            ->where('sender_id', '!=', Auth::id())
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'Messages marked as read']);
    }
}
