<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Mail' }}</title>
</head>
<body>
    <h2>Hallo Paule,</h2>
    <p>Ein neuer Benutzer Namens {{ $nick }} hat sich auf {{request()->getHost()}} registriert<br />
        <br />
    <p>👉 <a href="{{ $link }}">Zum Profil</a></p>
</body>
</html>
