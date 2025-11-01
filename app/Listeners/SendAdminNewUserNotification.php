<?php

namespace App\Listeners;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;

class SendAdminNewUserNotification
{
    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $user = $event->user;

        // Admin-Mail hier eintragen
        $adminEmail = 'parie@gmx.de';

        // Mail senden


        Mail::send([], [], function ($message) use ($adminEmail, $user) {
            $message->to($adminEmail)
                ->subject('Neuer Benutzer auf '.request()->getHost().' registriert')
                ->html(
                    "<h2>Hallo Admin,</h2>
                    <p>Ein neuer Benutzer hat sich auf ".request()->getHost()." registriert:</p>
                    <ul>
                        <li>Name: {$user->name}</li>
                        <li>Email: {$user->email}</li>
                    </ul>"
                );
        });

    }
}
