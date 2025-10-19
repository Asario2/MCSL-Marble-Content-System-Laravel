<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Mail' }}</title>
</head>
<body>
Sehr geehrte(r) {{$nick}}

    {{$content}}

Herzliche Grüße<br />
Asario
{{ $signatur}}
</body>
</html>

