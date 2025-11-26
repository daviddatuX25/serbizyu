<?php

namespace App\Notifications;

use App\Domains\Orders\Models\Order;
use App\Domains\Messaging\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Message $message;
    public Order $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Message $message, Order $order)
    {
        $this->message = $message;
        $this->order = $order;
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
        $orderUrl = route('orders.show', $this->order);

        return (new MailMessage)
            ->subject("New message in Order #{$this->order->id}")
            ->line("$senderName sent you a message regarding Order #{$this->order->id}.")
            ->line("Message: " . \Str::limit($this->message->content, 100))
            ->action('View Order', $orderUrl)
            ->line('Thank you for using ' . config('app.name') . '!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message_id' => $this->message->id,
            'order_id' => $this->order->id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'content_preview' => \Str::limit($this->message->content, 100),
            'url' => route('orders.show', $this->order),
        ];
    }
}
