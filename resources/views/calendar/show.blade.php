<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>G05 当日詳細一覧</title>
    <link rel="stylesheet" href="{{ asset('css/day.css') }}">
    @include('parts.head')
</head>
<body>
    @include('parts.header')
<div class="page-container">
<div class="page-layout">

  {{-- 左：メインエリア --}}
  <div class="main-area">

    <h1>{{ $date->format('Y年m月d日') }}</h1>

    <hr>

    <h2>予定 / タスク一覧</h2>

<div class="scroll-box">
  <table class="task-table">
    <thead>
      <tr>
        <th>時間</th>
        <th>タイトル</th>
        <th>カテゴリ</th>
        <th>状態</th>
        <th>場所</th>
        <th>メモ</th>
        <th>編集</th>
      </tr>
    </thead>

    <tbody>
    @forelse ($items as $item)
      <tr>
        <td>
          {{ optional($item->sche_start)->format('H:i') }}  
          {{ optional($item->sche_end)->format('H:i') }}
        </td>

        <td>{{ $item->title }}</td>

        {{-- カテゴリ（予定 / タスク） --}}
        <td>
          {{ $item->subcategory->subcategory ?? '予定' }}
        </td>

        {{-- 状態 --}}
        <td>
          @if ($item->status_id == 1)
            未
          @else
            済
          @endif
        </td>

        {{-- 場所 --}}
        <td>{{ $item->location ?? '-' }}</td>

        {{-- メモ --}}
        <td>{{ $item->memo }}</td>

        {{-- 編集のみ --}}
        <td>
          <a
            href="{{ url('/calendar/' . $item->id . '/edit') }}?date={{ $date->format('Y-m-d') }}"
            class="btn-edit"
          >
            編集
          </a>
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="7">今日の予定 / タスクはありません。</td>
      </tr>
    @endforelse
    </tbody>
  </table>
</div>


    <hr>

    <h2>今日の収入 / 支出</h2>
<div class="account-area">
<div class="scroll-box account-scroll">
  <table class="account-table">
    <thead>
      <tr>
        <th>種別</th>
        <th>金額</th>
        <th>タイトル</th>
        <th>カテゴリ</th>
        <th>メモ</th>
        <th>編集</th>
      </tr>
    </thead>

    <tbody>
    @forelse ($accounts as $account)
      <tr>
        <td>{{ $kubun[$account->type_id] }}</td>

        <td>{{ number_format($account->amount) }}円</td>

        <td>{{ $account->title }}</td>

        {{-- カテゴリ（仮） --}}
        <td>{{ $category[$account->subcategory_id] }}</td>

        <td>{{ $account->memo }}</td>

        <td>
          <a
            href="{{ url('/accounts/' . $account->id . '/edit') }}?date={{ $date->format('Y-m-d') }}"
            class="btn-edit"
          >
            編集
          </a>
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="6">今日の収支はありません。</td>
      </tr>
    @endforelse
    </tbody>
  </table>
</div>


  {{-- 右：todayカード --}}
  <aside class="today-card">
    <h3>today</h3>

    <div class="today-item task">
      <div>予定 / タスク</div>
      <span>{{ $items->count() }}件</span>
    </div>

    <div class="today-item income">
      <div>収入</div>
      <span>{{ number_format($incomeTotal) }}円</span>
    </div>

    <div class="today-item expense">
      <div>支出</div>
      <span>{{ number_format(abs($expenseTotal)) }}円</span>
    </div>
  </aside>

</div>
</div>
</body>

</html>
