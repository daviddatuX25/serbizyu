<?php

namespace App\Notifications;

use App\Domains\Listings\Models\OpenOfferBid;
use App\Domains\Messaging\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BidMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Message $message;
    public OpenOfferBid $bid;

    /**
     * Create a new notification instance.
     */
    public function __construct(Message $message, OpenOfferBid $bid)
    {
        $this->message = $message;
        $this->bid = $bid;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $senderName = $this->message->sender->name;
        $bidUrl = route('bids.messages.index', ['bid' => $this->bid]);
        $offerTitle = $this->bid->openOffer->title;

        return (new MailMessage)
            ->subject("New message in bid for: $offerTitle")
            ->line("$senderName sent you a message regarding a bid for \"$offerTitle\".")
            ->line("Message: " . \Str::limit($this->message->content, 100))
            ->action('View Bid Discussion', $bidUrl)
            ->line('Thank you for using ' . config('app.name') . '!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message_id' => $this->message->id,
            'bid_id' => $this->bid->id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'offer_title' => $this->bid->openOffer->title,
            'content_preview' => \Str::limit($this->message->content, 100),
            'url' => route('bids.messages.index', ['bid' => $this->bid]),
        ];
    }
}
