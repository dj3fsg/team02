<!DOCTYPE html>
<html lang="ja">

<head>
    @include('parts.head')
</head>

<body>
    @include('parts.errors')
    @include('parts.header')
    @yield('content')
</body>

</html>