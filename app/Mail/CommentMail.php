<?php
// app/Mail/WelcomeMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CommentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $domain;
    public $url;
    public $nickname;
    public $content;

    public function __construct($domain, $url,$nickname,$content)
    {
        $this->domain = $domain;
        $this->url = $url;
        $this->nickname = $nickname;
        $this->content = $content;
    }

    public function build()
    {
        return $this->subject($this->domain)
                ->markdown('emails.comments')
                ->with([
                    'domain' => $this->domain,
                    'url' => $this->url,
                    'nickname'=>$this->nickname,
                    'content' => $this->content,
                ]);
    }
}
