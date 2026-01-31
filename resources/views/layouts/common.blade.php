<!DOCTYPE html>
<html lang="ja">

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @include('parts.head')
</head>

<body>
    @include('parts.errors')
    @include('parts.header')
    @yield('content')
</body>

</html>