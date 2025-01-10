<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

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
            ->subject($this->data['subject'])
            ->line($this->data['message'])
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return $this->data;
    }
}
