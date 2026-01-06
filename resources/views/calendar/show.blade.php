<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>G05 当日詳細一覧</title>

  @include('parts.head')

  <link rel="stylesheet" href="{{ asset('css/day.css') }}?v={{ filemtime(public_path('css/day.css')) }}">
</head>

<body>
  @include('parts.header')

  <div class="page-container">
    <div class="page-layout">

      {{-- 左：メインエリア --}}
      <div class="main-area">

        <h1>{{ $date->format('Y年m月d日') }}</h1>

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
                  @if ($item->sche_start)
                    {{ \Carbon\Carbon::parse($item->sche_start)->format('H:i') }}
                    @if ($item->sche_end)
                      〜 {{ \Carbon\Carbon::parse($item->sche_end)->format('H:i') }}
                    @endif
                  @else
                    -
                  @endif
                </td>

                <td>{{ $item->title }}</td>

                <td>{{ $item->subcategory->subcategory ?? '予定' }}</td>

                <td>
                  @if ($item->status_id == 1)
                    未
                  @else
                    済
                  @endif
                </td>

                <td>{{ $item->location ?? '-' }}</td>

                <td>{{ $item->memo }}</td>

                <td class="edit-cell">
                  <a href="{{ route('items.edit', $item->id) }}" class="btn-edit">編集</a>
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
                  <td>{{ $subcategories[$account->subcategory_id] ?? '-' }}</td>

                  <td>{{ number_format($account->amount) }}円</td>

                  <td>{{ $account->title }}</td>

                  <td>{{ $category[$account->account_category_id] ?? '-' }}</td>

                  <td>{{ $account->memo }}</td>

                  <td class="edit-cell">
                    <a href="{{ route('accounts.edit', $account->id) }}" class="btn-edit">編集</a>
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

          <div class="today-compact">
            <!-- 上段 -->
            <div class="tc-block left">
              <div class="tc-title">予定/タスク</div>
              <div class="tc-value">{{ $items->count() }}件</div>
            </div>

            <div class="tc-block right">
              <div class="tc-title">収入</div>
              <div class="tc-value income">{{ number_format($incomeTotal) }}円</div>
            </div>

            <!-- 下段 -->
            <div class="tc-block left">
              <div class="tc-title">収支合計</div>
              <div class="tc-value {{ $netDay >= 0 ? 'income' : 'expense' }}">
                {{ $netDay >= 0 ? '+' : '-' }}{{ number_format(abs($netDay)) }}円
              </div>
            </div>

            <div class="tc-block right">
              <div class="tc-title">支出</div>
              <div class="tc-value expense">{{ number_format(abs($expenseTotal)) }}円</div>
            </div>
          </div>

          <div class="today-divider"></div>

          <div class="today-month">
            <div class="label">今月の収支合計</div>
            <div class="value"
              style="color: {{ $netMonth >= 0 ? '#118a2a' : '#c21c1c' }};">
              {{ $netMonth >= 0 ? '+' : '-' }}{{ number_format(abs($netMonth)) }}円
            </div>
          </div>
        </aside>


        </div>
      </div>
    </div>
  </div>
</body>
</html>
