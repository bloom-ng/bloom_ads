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

        return (new MailMessage)->view('emails.organization-invitation', [
            'organizationName' => $this->organization->name,
            'role' => $this->invite->role,
            'url' => $url
        ])->subject('You\'ve been invited to join ' . $this->organization->name);
    }
}
