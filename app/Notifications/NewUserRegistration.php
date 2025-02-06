<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserRegistration extends Notification implements ShouldQueue
{
    use Queueable;

    protected $newUser;

    public function __construct(User $user)
    {
        $this->newUser = $user;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New User Registration')
            ->line('A new user has registered on the platform.')
            ->line('Name: ' . $this->newUser->name)
            ->line('Email: ' . $this->newUser->email)
            ->line('Business Name: ' . $this->newUser->business_name)
            ->line('User Type: ' . $this->newUser->user_type)
            ->action('View User', url('/admin/users'))
            ->line('Thank you for using our application!');
    }
}
