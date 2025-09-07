<!DOCTYPE html>
<html>
<body>
    <h1>Alle Links</h1>
    <ul>
        @foreach($routes as $route)
            <li><a href="{{ url($route) }}">{{ $route }}</a></li>
        @endforeach
    </ul>
</body>
</html>
