<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
        <h1>
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('img/logo_onecal_str.png') }}" alt="" height="51">
            </a>
        </h1>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto">
            </ul>
            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                <!-- Authentication Links -->
                @guest
                @if (Route::has('login'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                </li>
                @endif
                @if (Route::has('register'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                </li>
                @endif
                @else
                <li class="nav-item">
                    <a class="nav-link" href="/calendar">@can('isAdmin')★@endcan{{ Auth::user()->name }}のカレンダー</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/calendar/create">予定作成</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/accounts/create">収支作成</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('users.edit', ['id' => Auth::user()->id]) }}">ユーザー編集</a>
                </li>
                @can('isAdmin')
                <li class="nav-item">
                    <a class="nav-link" href="/users">★ユーザー一覧</a>
                </li>
                @endcan
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link logout-btn">
                            ログアウト
                        </button>
                    </form>
                </li>

                @endguest
            </ul>
        </div>
    </div>
</nav>