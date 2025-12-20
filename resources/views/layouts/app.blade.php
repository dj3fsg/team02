<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
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