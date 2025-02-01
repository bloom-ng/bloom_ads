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
        return (new MailMessage)
            ->view('emails.base-notification', [
                'subject' => $this->data['subject'],
                'messages' => $this->data['message'],
                'actionText' => $this->data['action_text'] ?? null,
                'actionUrl' => $this->data['action_url'] ?? null
            ])
            ->subject($this->data['subject']);
    }

    public function toArray($notifiable)
    {
        return $this->data;
    }
}
