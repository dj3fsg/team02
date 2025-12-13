<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>G05 当日詳細一覧</title>
</head>
<body>
    <h1>G05 当日詳細一覧</h1>
    <p>日付：{{ $date }}</p>

    <hr>

    <h2>予定/タスク一覧</h2>

    <table border="1" cellpadding="5">
        <tr>
            <th>時間</th>
            <th>タイトル</th>
            <th>状態</th>
        </tr>

    @forelse($items as $item)
        <tr>
            <td>{{ optional($item->sche_start)->format('H:i') }}
                -
                {{ optional($item->sche_end)->format('H:i') }}
            </td>
            <td>{{ $item->title }}</td>
            <td>{{ $item->status_id }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="3">予定はありません。</td>
        </tr>
    @endforelse
    </table>>

    <hr>

    <h2>今日の収入/支出</h2>
    
    <table border="1" cellpadding="5">
        <tr>金額</tr>
        <tr>タイトル</tr>

    @forelse($accounts as $account)
        <tr>
            <td>{{ $account->amount }}円</td>
            <td>{{ $account->title }}</td>
    @empty
        <tr>
            <td colspan="2">収支はありません</td>
        </tr>
    @endforelse

<h3>今日の合計</h3>

<p>収入合計：{{ number_format($incomeTotal) }}円</p>
<p>支出合計：{{ number_format(abs($expenseTotal)) }}円</p>
</body>
</html>