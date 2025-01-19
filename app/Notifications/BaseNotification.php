<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BaseNotification extends Notification
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage)
            ->subject($this->data['subject'])
            ->line($this->data['message']);

        if (isset($this->data['action_url'])) {
            $mailMessage->action($this->data['action_text'] ?? 'View Details', $this->data['action_url']);
        }

        return $mailMessage;
    }

    public function toArray($notifiable)
    {
        return $this->data;
    }
}
