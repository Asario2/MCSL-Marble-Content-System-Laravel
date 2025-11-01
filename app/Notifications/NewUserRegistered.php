<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewUserRegistered extends Notification
{
    use Queueable;

    public $user;  // der neue Benutzer

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['mail']; // nur per E-Mail
    }

    public function toMail($notifiable)
    {
        $link = url("/users/{$this->user->id}");

        return (new MailMessage)
            ->subject("Neuer Benutzer auf " . request()->getHost())
            ->greeting("Hallo Paule,")
            ->line("Ein neuer Benutzer namens {$this->user->name} hat sich auf " . request()->getHost() . " registriert.")
            ->action('Zum Profil', $link)
            ->line('Danke, dass du das System betreust!');
    }
}
