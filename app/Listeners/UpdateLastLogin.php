<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateLastLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        // Update user's last login
        $user->update([
            'last_login_at' => now(),
        ]);

        // If user belongs to a salon, update salon's last login
        if ($user->salon) {
            $user->salon->updateLastLogin();
        }
    }
}
