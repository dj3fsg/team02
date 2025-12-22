<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">

    {{-- FullCalendar --}}
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

    <title>カレンダー</title>

    <style>
        body {
            font-family: sans-serif;
        }

        .calendar-layout {
            display: flex;
            gap: 24px;
            max-width: 1400px;
            margin: 32px auto;
        }

        #calendar {
            flex: 1;
        }

        .side-panel {
            width: 320px;
            border: 1px solid #ccc;
            padding: 12px;
            border-radius: 8px;
            background: #fff;
        }

        .fc-count {
            font-size: 13px;
            text-align: center;
            margin-top: 2px;
            pointer-events: none;
        }

        .fc-expense {
            font-size: 13px;
            text-align: center;
            margin-top: 2px;
            background: #e6f4ea;
            color: #256029;
            padding: 2px 4px;
            border-radius: 4px;
            display: inline-block;
            pointer-events: none;
        }

        .fc-daygrid-day {
            cursor: pointer;
        }

        .fc-day-today {
            background: #fff4f4 !important;
        }

        .side-panel ul {
            padding-left: 0;
            list-style: none;
        }

        .side-panel li {
            font-size: 13px;
            margin-bottom: 6px;
        }
    </style>
</head>
<body>

<div class="calendar-layout">

    <!-- 左：カレンダー -->
    <div id="calendar"></div>

    <!-- 右：サイドパネル -->
    <aside class="side-panel">
        <h3 id="selected-date">日付を選択</h3>

        <section>
            <h4>今日の予定 / タスク</h4>
            <ul id="side-items"></ul>
        </section>

        <section>
            <h4>今日の収入 / 支出</h4>
            <ul id="side-accounts"></ul>
        </section>
    </aside>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const itemCounts  = @json($itemCounts);
    const expenseSums = @json($expenseSums);
    const items       = @json($items);
    const accounts    = @json($accounts);

    const calendarEl = document.getElementById('calendar');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'ja',
        timeZone: 'local',
        height: 'auto',

        headerToolbar: {
            left: 'prev',
            center: 'title',
            right: 'next'
        },

        dateClick: function(info) {
            const dateStr = info.dateStr;
            document.getElementById('selected-date').textContent = dateStr;

            // 予定表示
            const itemList = document.getElementById('side-items');
            itemList.innerHTML = '';

            items
                .filter(i => i.sche_start.startsWith(dateStr))
                .forEach(i => {
                    const time = i.sche_start.slice(11, 16);
                    const status = i.status_id == 1 ? '未' : '済';
                    const type = i.subcategory_id == 3 ? '予定' : 'タスク';

                    const li = document.createElement('li');
                    li.textContent = `[${status}] ${type} ${time} ${i.title}`;
                    itemList.appendChild(li);
                });

            // 収支表示
            const accList = document.getElementById('side-accounts');
            accList.innerHTML = '';

            accounts
                .filter(a => a.date === dateStr)
                .forEach(a => {
                    const sign = a.subcategory_id == 2 ? '-' : '+';
                    const li = document.createElement('li');
                    li.textContent = `${sign} ¥${Number(a.amount).toLocaleString()} ${a.title}`;
                    accList.appendChild(li);
                });
        },

        dayCellDidMount: function(info) {
            const dateStr =
                info.date.getFullYear() + '-' +
                String(info.date.getMonth() + 1).padStart(2, '0') + '-' +
                String(info.date.getDate()).padStart(2, '0');

            const frame = info.el.querySelector('.fc-daygrid-day-frame');
            if (!frame) return;

            let html = '';

            if (itemCounts[dateStr]) {
                html += `<div class="fc-count">予定：${itemCounts[dateStr]}件</div>`;
            }

            if (expenseSums[dateStr]) {
                html += `<div class="fc-expense">支出：¥${Number(expenseSums[dateStr]).toLocaleString()}</div>`;
            }

            if (html) {
                const box = document.createElement('div');
                box.innerHTML = html;
                frame.appendChild(box);
            }
        }
    });

    calendar.render();
});
</script>

</body>
</html>
