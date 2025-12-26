<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">

    {{-- FullCalendar --}}
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/calendar.css') }}">
    <title>カレンダー</title>
<<<<<<< HEAD
=======

    <style>
        body {
            font-family: sans-serif;
        }

        #calendar {
            max-width: 1100px;
            margin: 40px auto;
        }

        .fc-count {
            font-size: 12px;
            margin-top: 4px;
            pointer-events: none;
        }

        .fc-expense {
            font-size: 12px;
            margin-top: 2px;
            background: #c8f7c5;
            padding: 2px 4px;
            border-radius: 4px;
            display: inline-block;
            pointer-events: none;
        }

        .calendar-layout {
            display: flex;
            gap: 24px;
            max-width: 1400px;
            margin: 0 auto;
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
    </style>
>>>>>>> 323b3f784cbabd012d7d3476c5b0f55a9a9a727b
    @include('parts.head')
</head>

<body>
    @include('parts.header')
<<<<<<< HEAD
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
=======

    <div id="calendar"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const itemCounts = @json($itemCounts);
            const expenseSums = @json($expenseSums);

            let calendarEl = document.getElementById('calendar');

            let calendar = new FullCalendar.Calendar(calendarEl, {
                dateClick: function(info) {
                    // info.dateStr は "2025-12-17" 形式
                    window.location.href = `/calendar/events/${info.dateStr}`;
                },

                initialView: 'dayGridMonth',
                timeZone: 'local',
                locale: 'ja',
                height: 'auto',
                headerToolbar: {
                    left: 'prev',
                    center: 'title',
                    right: 'next'
                },

                dayCellDidMount: function(info) {
                    const dateStr =
                        info.date.getFullYear() + '-' +
                        String(info.date.getMonth() + 1).padStart(2, '0') + '-' +
                        String(info.date.getDate()).padStart(2, '0');

                    let html = '';

                    if (itemCounts[dateStr]) {
                        html += `<div style="font-size:12px;">予定：${itemCounts[dateStr]}件</div>`;
                    }

                    if (expenseSums[dateStr]) {
                        html += `<div style="
                        font-size:12px;
                        background:#c8f7c5;
                        display:inline-block;
                        padding:2px 4px;
                        border-radius:4px;
                    ">
                        支出：¥${Number(expenseSums[dateStr]).toLocaleString()}
                    </div>`;
                    }

                    if (html) {
                        const wrapper = document.createElement('div');
                        wrapper.innerHTML = html;
                        info.el.appendChild(wrapper);
                    }
                }
            });

            calendar.render();
        });
    </script>
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
>>>>>>> 323b3f784cbabd012d7d3476c5b0f55a9a9a727b

<script>
document.addEventListener('DOMContentLoaded', function () {

    const itemCounts  = @json($itemCounts);
    const incomeSums  = @json($incomeSums);
    const expenseSums = @json($expenseSums);
    const items       = @json($items);
    const accounts    = @json($accounts);

    let clickTimer = null;
    let clickedDate = null;

    const calendar = new FullCalendar.Calendar(
        document.getElementById('calendar'), {

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

            // ダブルクリック判定 → G05へ遷移
            if (clickedDate === dateStr && clickTimer) {
                clearTimeout(clickTimer);
                clickTimer = null;
                clickedDate = null;

                window.location.href = `/calendar/events/${dateStr}`;
                return;
            }

            // シングルクリック → 右サイド表示
            clickedDate = dateStr;
            clickTimer = setTimeout(() => {
                showSidePanel(dateStr);
                clickTimer = null;
                clickedDate = null;
            }, 300);
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

            if (incomeSums[dateStr]) {
                html += `<div class="fc-income">収入：¥${Number(incomeSums[dateStr]).toLocaleString()}</div>`;
            }

            if (expenseSums[dateStr]) {
                html += `<div class="fc-expense">支出：¥${Number(expenseSums[dateStr]).toLocaleString()}</div>`;
            }

            if (html) {
                const box = document.createElement('div');
                box.className = 'fc-summary';
                box.innerHTML = html;
                frame.appendChild(box);
            }
        }
    });

    calendar.render();

    // 右サイド表示処理
    function showSidePanel(dateStr) {
        document.getElementById('selected-date').textContent = dateStr;

        // 予定
        const itemList = document.getElementById('side-items');
        itemList.innerHTML = '';
        const todayItems = items.filter(i => i.sche_start.startsWith(dateStr));

        if (todayItems.length === 0) {
            itemList.innerHTML = '<li>予定はありません</li>';
        } else {
            todayItems.forEach(i => {
                const time = i.sche_start.slice(11, 16);
                const status = i.status_id == 1 ? '未' : '済';
                const type = i.subcategory_id == 3 ? '予定' : 'タスク';

                const li = document.createElement('li');
                li.textContent = `[${status}] ${type} ${time} ${i.title}`;
                itemList.appendChild(li);
            });
        }

        // 収支
        const accList = document.getElementById('side-accounts');
        accList.innerHTML = '';
        const todayAcc = accounts.filter(a => a.date === dateStr);

        if (todayAcc.length === 0) {
            accList.innerHTML = '<li>収支の登録はありません</li>';
        } else {
            todayAcc.forEach(a => {
                // 2 = 支出、それ以外 = 収入
                const sign = a.subcategory_id == 2 ? '-' : '+';
                const li = document.createElement('li');
                li.textContent = `${sign} ¥${Number(a.amount).toLocaleString()} ${a.title}`;
                accList.appendChild(li);
            });
        }
    }

});
</script>

</body>

</html>