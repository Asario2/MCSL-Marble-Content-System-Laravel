<?php

namespace App\Services;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

class SimpleMailService
{
    public static function send(string $to, string $subject, string $html): void
    {
        $dsn = sprintf(
            'smtp://%s:%s@%s:%s?encryption=tls',
            urlencode(env('MAIL_USERNAME')),
            urlencode(env('MAIL_PASSWORD')),
            env('MAIL_HOST'),
            env('MAIL_PORT', 587)
        );

        $transport = Transport::fromDsn($dsn);
        $mailer = new Mailer($transport);

        $email = (new Email())
            ->from(env('MAIL_FROM_ADDRESS'))
            ->to($to)
            ->subject($subject)
            ->html($html);

        $mailer->send($email);
    }
}
