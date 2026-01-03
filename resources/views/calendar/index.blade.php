<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">

    {{-- FullCalendar --}}
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/calendar.css') }}">
    <title>カレンダー</title>
        
    @include('parts.head')
</head>

<body>
    @include('parts.header')

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
</div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        const itemCounts  = @json($itemCounts);
        const incomeSums  = @json($incomeSums);
        const expenseSums = @json($expenseSums);
        const items       = @json($items);
        const accounts    = @json($accounts);

        let clickTimer = null;
        let clickedDate = null;

        // ==========================
        // 祝日データ取得 → カレンダー生成
        // ==========================
        fetch('https://holidays-jp.github.io/api/v1/date.json')
            .then(res => res.json())
            .then(holidays => {

                const calendar = new FullCalendar.Calendar(
                    document.getElementById('calendar'), {

                    initialView: 'dayGridMonth',
                    locale: 'ja',
                    timeZone: 'local',
                    height: 'auto',
                    fixedWeekCount: false,

                    headerToolbar: {
                        left: 'prev',
                        center: 'title',
                        right: 'next'
                    },

                    // 「日」を消して数字だけ表示
                    dayCellContent: function(arg) {
                        return { html: `<span>${arg.date.getDate()}</span>` };
                    },

                    dateClick: function(info) {
                        const dateStr = info.dateStr;

                        document.querySelectorAll('.fc-day-selected')
                            .forEach(el => el.classList.remove('fc-day-selected'));

                        info.dayEl.classList.add('fc-day-selected');

                        if (clickedDate === dateStr && clickTimer) {
                            clearTimeout(clickTimer);
                            clickedDate = null;
                            window.location.href = `/calendar/events/${dateStr}`;
                            return;
                        }

                        clickedDate = dateStr;
                        clickTimer = setTimeout(() => {
                            showSidePanel(dateStr);
                            clickedDate = null;
                        }, 300);
                    },
                    dayCellDidMount: function(info) {
                const dateStr =
                    info.date.getFullYear() + '-' +
                    String(info.date.getMonth() + 1).padStart(2, '0') + '-' +
                    String(info.date.getDate()).padStart(2, '0');

                const top = info.el.querySelector('.fc-daygrid-day-top');
                const frame = info.el.querySelector('.fc-daygrid-day-frame');
                if (!top || !frame) return;

                /* ===== 日付 + 祝日を横並びにする ===== */
                const wrapper = document.createElement('div');
                wrapper.className = 'fc-day-label';

                // 日付数字
                const numberEl = top.querySelector('.fc-daygrid-day-number');
                if (numberEl) {
                    wrapper.appendChild(numberEl);
                }

                // 祝日
                if (holidays[dateStr]) {
                    info.el.classList.add('fc-day-holiday');

                    const holidayEl = document.createElement('span');
                    holidayEl.className = 'fc-holiday-label';
                    holidayEl.textContent = holidays[dateStr];

                    wrapper.appendChild(holidayEl);
                }

                // top を差し替え
                top.innerHTML = '';
                top.appendChild(wrapper);

                /* ===== サマリー ===== */
                let html = '';

                if (itemCounts[dateStr]) {
                    html += `<div class="fc-count">予定/タスク：${itemCounts[dateStr]}件</div>`;
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
            });

        // ==========================
        // 右サイド表示
        // ==========================
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

                    console.log('item確認', i);

                    const time = i.sche_start.slice(11, 16);
                    const status = i.status_id == 1 ? '未' : '済';

                    const type =
                        i.subcategory_id == 1 ? '予定' :
                        i.subcategory_id == 2 ? 'タスク' :
                        '不明';

                    const li = document.createElement('li');
                    li.textContent = `【${type}】[${status}] ${time} ${i.title}`;
                    itemList.appendChild(li);
                });
            }

            // 収支
            const accList = document.getElementById('side-accounts');
            accList.innerHTML = '';
            const todayAcc = accounts.filter(a => {
                const d = new Date(a.date);
                return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}` === dateStr;
            });

            if (todayAcc.length === 0) {
                accList.innerHTML = '<li>収支の登録はありません</li>';
            } else {
                todayAcc.forEach(a => {
                    const isExpense = a.subcategory_id == 4;
                    const label = isExpense ? '支出' : '収入';
                    const sign  = isExpense ? '-' : '+';

                    const li = document.createElement('li');
                    li.classList.add(isExpense ? 'expense' : 'income');
                    li.textContent = `${label} ${sign}¥${Number(a.amount).toLocaleString()} ${a.title}`;
                    accList.appendChild(li);
                });
            }
        }

    });
    </script>

</body>

</html>