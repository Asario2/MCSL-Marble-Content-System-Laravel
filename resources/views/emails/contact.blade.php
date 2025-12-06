@component('mail::message')
# Neue Email auf {{ $domain }}

von **{{ $nickname }}** ({{$email}})

<b>{!! nl2br(e($content)) !!}</b>

**{{ config('app.name') }} Team**
@endcomponent
