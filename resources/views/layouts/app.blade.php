<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @include('parts.head')    
</head>

<body>
    @include('parts.errors')
    <div id="app">
        @include('parts.header')

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>

</html>