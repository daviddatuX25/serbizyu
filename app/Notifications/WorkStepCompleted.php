<?php

namespace App\Notifications;

use App\Domains\Work\Models\WorkInstanceStep;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkStepCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public WorkInstanceStep $workInstanceStep
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
        $workInstance = $this->workInstanceStep->workInstance;
        $order = $workInstance->order;
        $service = $order->service;

        return (new MailMessage)
            ->subject('Work Step Completed: ' . ($service?->title ?? 'Your Order'))
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line('A work step has been completed for your order.')
            ->line('**Service:** ' . ($service?->title ?? 'N/A'))
            ->line('**Step:** ' . ($this->workInstanceStep->workTemplate?->name ?? 'Step ' . ($this->workInstanceStep->step_index + 1)))
            ->line('**Progress:** ' . $workInstance->getProgressPercentage() . '% complete')
            ->action('View Order', route('orders.show', $order))
            ->line('Thank you for using Serbizyu!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $workInstance = $this->workInstanceStep->workInstance;
        $order = $workInstance->order;
        $service = $order->service;

        return [
            'title' => 'Work Step Completed',
            'message' => 'Step "' . ($this->workInstanceStep->workTemplate?->name ?? 'Step ' . ($this->workInstanceStep->step_index + 1)) . '" completed for order #' . $order->id,
            'order_id' => $order->id,
            'work_instance_id' => $workInstance->id,
            'work_instance_step_id' => $this->workInstanceStep->id,
            'progress_percentage' => $workInstance->getProgressPercentage(),
            'service_title' => $service?->title ?? 'Service',
            'seller_name' => $order->seller->name,
            'buyer_name' => $order->buyer->name,
        ];
    }
}
