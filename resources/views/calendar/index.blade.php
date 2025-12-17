<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Calendar</title>
    <style>
        table {
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            width: 100px;
            height: 80px;
            vertical-align: top;
            padding: 4px;
        }
        th {
            background: #eee;
        }
        .today {
            background: #ffecec;
        }
        .count {
            font-size: 12px;
            margin-top: 4px;
        }
    </style>
</head>
<body>

<h1>{{ $year }}年 {{ $month }}月</h1>

<table>
    <tr>
        <th>日</th>
        <th>月</th>
        <th>火</th>
        <th>水</th>
        <th>木</th>
        <th>金</th>
        <th>土</th>
    </tr>

    <tr>
        @foreach ($dates as $date)
            <td class="{{ $date->isToday() ? 'today' : '' }}">

                {{-- 日付 --}}
                <a href="{{ route('day.show', $date->format('Y-m-d')) }}">
                    {{ $date->day }}
                </a>

                {{-- 予定件数 --}}
                <div class="count">
                    @if(isset($itemCounts[$date->format('Y-m-d')]))
                        予定：{{ $itemCounts[$date->format('Y-m-d')] }}件
                    @endif
                </div>

                {{-- 支出合計 --}}
                <div class="count">
                    @if(isset($expenseSums[$date->format('Y-m-d')]))
                        支出：¥{{ number_format($expenseSums[$date->format('Y-m-d')]) }}
                    @endif
                </div>

            </td>

            {{-- 土曜日で改行 --}}
            @if ($date->dayOfWeek === 6)
                </tr><tr>
            @endif
        @endforeach
    </tr>
</table>

</body>
</html>
