<?php

namespace App\Notifications;

use App\Domains\Work\Models\ActivityMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ActivityMessageCreated extends Notification
{
    use Queueable;

    public ActivityMessage $activityMessage;

    /**
     * Create a new notification instance.
     */
    public function __construct(ActivityMessage $activityMessage)
    {
        $this->activityMessage = $activityMessage;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Activity Message')
            ->line('A new activity message has been posted:')
            ->line($this->activityMessage->content)
            ->action('View Activity', url('/work-instances/' . $this->activityMessage->activityThread->workInstanceStep->workInstance->id . '/steps/' . $this->activityMessage->activityThread->workInstanceStep->id));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'activity_message_id' => $this->activityMessage->id,
            'activity_thread_id' => $this->activityMessage->activity_thread_id,
            'content' => $this->activityMessage->content,
        ];
    }
}
