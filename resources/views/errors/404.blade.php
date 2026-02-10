@extends('layouts.common')

@section('content')
  <div class="container text-center py-5">
    <h1 class="display-6 mb-3">404 Not Found</h1>
    <p class="text-muted mb-4">
      お探しのページは見つかりませんでした。
    </p>

    <a href="{{ url('calendar') }}" class="btn btn-primary">
      カレンダーへ戻る
    </a>
  </div>
@endsection
