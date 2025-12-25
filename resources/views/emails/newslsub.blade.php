<div style="font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
@php
    echo MCSL_GRAD();
@endphp
<h1>Nur noch ein Klick bis zu deiner Anmeldung!</h1>

@php
    $name = $nick
    ? htmlentities($nick, ENT_QUOTES, 'UTF-8')
    : 'liebe Leserin, lieber Leser';
@endphp

<p>Hallo <strong>{{ $name }}</strong>,</p>

<p>
    Du hast dich für <strong>Asarios</strong> Newsletter angemeldet.<br>
    Um deine Anmeldung abzuschließen, bestätige bitte deine E-Mail-Adresse über den folgenden Link:
</p>

<p style="text-align:left; margin: 20px 20px;">
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
</div>
