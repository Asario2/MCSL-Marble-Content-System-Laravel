@component('mail::message')

Es wurde ein neuer Kommentar von **{{ $nickname }}** auf **https://{{ request()->getHost() }}** veröffentlicht.

<div class='tro'>Kommentar: <b>{{ $content }}</b></div>

@component('mail::button', ['url' => $url])
Jetzt Überprüfen
@endcomponent


Mit freundlichen Grüßen,
**{{ config('app.name') }} Team**
@endcomponent
