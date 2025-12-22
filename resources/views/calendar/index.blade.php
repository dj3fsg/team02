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
</head>
<body>

    <div id="calendar"></div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
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



</body>
</html>
