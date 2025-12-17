<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>G05 当日詳細一覧</title>
</head>
<body>
    <h1>{{ $date->format('Y年m月d日') }}</h1>


    <hr>

    <h2>予定/タスク</h2>

    <table border="1" cellpadding="5">
        <tr>
            <th>時間</th>
            <th>タイトル</th>
            <th>状態</th>
            <th>メモ</th>
        </tr>


    @forelse($items as $item)
        <tr>
            <td>{{ optional($item->sche_start)->format('H:i') }}
                -
                {{ optional($item->sche_end)->format('H:i') }}
            </td>
            <td>{{ $item->title }}</td>
            <td>
                @if ($item->status_id == 1)
                    未
                @elseif ($item->status_id == 2)
                    済
                @else
                    -
                @endif
            </td>

            <td>{{ $item->memo }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="4">予定はありません。</td>
        </tr>
    @endforelse
    </table>>

    <hr>

    <h2>収入/支出</h2>
    
    <table border="1" cellpadding="5">
        <tr>
            <th>種別</th>
            <th>金額</th>
            <th>タイトル</th>
            <th>メモ</th>
        </tr>

    @forelse ($accounts as $account)
        <tr>
            <td>
                {{ $account->subcategory_id == 3 ? '収入' : '支出' }}
            </td>
            <td>{{ number_format($account->amount) }}円</td>
            <td>{{ $account->title }}</td>
            <td>{{ $account->memo }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="4">データはありません</td>
        </tr>
    @endforelse

<h3>今日の合計</h3>

<p>収入合計：{{ number_format($incomeTotal) }}円</p>
<p>支出合計：{{ number_format(abs($expenseTotal)) }}円</p>
</body>
</html>