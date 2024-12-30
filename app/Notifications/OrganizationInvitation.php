<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Organization;
use App\Models\OrganizationInvite;

class OrganizationInvitation extends Notification
{
    // use Queueable;

    protected $organization;
    protected $invite;

    public function __construct(Organization $organization, OrganizationInvite $invite)
    {
        $this->organization = $organization;
        $this->invite = $invite;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url("/signup/invite?email={$this->invite->email}&token={$this->invite->token}");

        return (new MailMessage)
            ->subject('You\'ve been invited to join ' . $this->organization->name)
            ->greeting('Hello!')
            ->line('You have been invited to join ' . $this->organization->name . ' on Bloom Ads.')
            ->line('You have been assigned the role of ' . $this->invite->role . '.')
            ->action('Accept Invitation', $url)
            ->line('This invitation will expire in 7 days.')
            ->line('If you did not expect this invitation, you can ignore this email.');
    }
}
