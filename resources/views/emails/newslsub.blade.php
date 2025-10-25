<div style="background-color:#000;background-position:right;height:52px;background-repeat:no-repeat;background-image:url('https://www.marblefx.de/_images/mailheader/mcsl_grad.png');">
        <div style="float:left;position:relative;margin-top:0px;margin-left:0px;">
        <img src="https://www.marblefx.de/_images/mailheader/mcsl_mail_system2.png" alt="MCS Mail System" title="MCS Mail System">
    </div></div><br>
<h1>Nur noch ein Klick bis zu deiner Anmeldung!</h1>

@php
    $name = html_entities($nick) ?: 'liebe Leserin, lieber Leser';
@endphp

<p>Hallo <strong>{{ $name }}</strong>,</p>

<p>
    Du hast dich für <strong>Asarios</strong> Newsletter angemeldet.<br>
    Um deine Anmeldung abzuschließen, bestätige bitte deine E-Mail-Adresse über den folgenden Link:
</p>

<p style="text-align:center; margin: 20px 0;">
    <a href="{{ $link }}" style="display:inline-block; padding:10px 20px; background:#2563eb; color:#fff; text-decoration:none; border-radius:5px;">
        Jetzt Anmeldung bestätigen
    </a>
</p>

<p>
    Erst nach der Bestätigung wirst du in unseren Verteiler aufgenommen.<br>
    Danach erhältst du regelmäßig spannende Neuigkeiten, Tipps und Updates.
</p>

<p>
    Falls du dich nicht selbst für unseren Newsletter angemeldet hast,
    kannst du diese E-Mail einfach ignorieren – es erfolgt keine Registrierung.
</p>

<p>
    Mit freundlichen Grüßen,<br>
    <strong>{{ config('app.name') }} Team</strong>
</p>
