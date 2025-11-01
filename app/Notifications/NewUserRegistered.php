<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserRegistered extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Neuer Benutzer auf deiner Seite')
            ->greeting('Hallo Admin,')
            ->line('Ein neuer Benutzer hat sich registriert:')
            ->line('Name: ' . $this->user->name)
            ->line('E-Mail: ' . $this->user->email)
            ->action('Zum Benutzer', url('/admin/users/' . $this->user->id))
            ->line('Viele Grüße,')
            ->line(config('app.name'));
    }
}
