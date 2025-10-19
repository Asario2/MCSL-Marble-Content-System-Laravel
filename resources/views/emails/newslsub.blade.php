@component('mail::message')
# Nur noch ein Klick bis zu deiner Anmeldung!
@php
    $name = $nick ?: 'liebe Leserin, lieber Leser';
@endphp
Hallo **{{ $name }}**,

du hast dich für **Asarios** Newsletter angemeldet.
Um deine Anmeldung abzuschließen, bestätige bitte deine E-Mail-Adresse über den folgenden Link:

@component('mail::button', ['url' => $link])
Jetzt Anmeldung bestätigen
@endcomponent

Erst nach der Bestätigung wirst du in unseren Verteiler aufgenommen.
Danach erhältst du regelmäßig spannende Neuigkeiten, Tipps und Updates.

Falls du dich nicht selbst für unseren Newsletter angemeldet hast,
kannst du diese E-Mail einfach ignorieren – es erfolgt keine Registrierung.

Mit freundlichen Grüßen,
**{{ config('app.name') }} Team**
@endcomponent
