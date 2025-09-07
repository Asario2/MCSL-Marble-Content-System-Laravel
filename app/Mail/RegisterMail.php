<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegisterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $link;
    public $nick;
    public $content;

    public function __construct($title, $link, $nick, $content)
    {
        $this->title = $title;
        $this->link = $link;
        $this->nick = $nick;
        $this->content = $content;
    }

    public function build()
    {
        return $this->subject($this->title)
                    ->view('emails.register')
                    ->with([
                        'link' => $this->link,
                        'nick' => $this->nick,
                        'content' => $this->content,
                    ]);
    }
}
