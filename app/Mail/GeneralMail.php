<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GeneralMail extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $link;
    public $nick;
    public $content;

    public function __construct($title, $link, $nick, $content,$view='newsletter',$signatur='')
    {
        $this->title = $title;
        $this->link = $link;
        $this->nick = $nick;
        $this->content = $content;
        $this->signatur = $signatur;
        $this->view = $view;
    }

    public function build()
    {
        return $this->subject($this->title)
                    ->markdown($this->view)
                    ->with([
                        'link' => $this->link,
                        'nick' => $this->nick,
                        'content' => html_entity_decode($this->content),
                        "signatur" => html_entity_decode($this->signatur),
                    ]);
    }
}

