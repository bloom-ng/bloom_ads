<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Admin;
use App\Mail\NewUserRegistrationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Send email to all admins about new user registration
        Log::info('Fetching all admins for notification');
        $admins = Admin::getAllAdmins();
        Log::info('Found admins to notify', ['admin_count' => $admins->count()]);

        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(new NewUserRegistrationMail($user));
                Log::info('Email sent successfully to admin', [
                    'admin_id' => $admin->id,
                    'admin_email' => $admin->email
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send email to admin', [
                    'admin_id' => $admin->id,
                    'admin_email' => $admin->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
    }
}
