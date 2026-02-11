<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  @include('parts.head')  <!-- Bootstrap等の共通は先に -->

  <!-- FullCalendar -->
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

  <!-- 画面固有CSS -->
  <link rel="stylesheet" href="{{ asset('css/calendar.css') }}?v={{ filemtime(public_path('css/calendar.css')) }}">
</head>

<body>
  @include('parts.header')

  <div class="calendar-layout">

    <!-- 左：カレンダー -->
    <div id="calendar"></div>

    <!-- 右：サイドパネル -->
    <aside class="side-panel" id="side-panel" data-date="">
      <h3 id="selected-date">日付を選択</h3>

      <section>
        <h4>今日の予定 / タスク</h4>
        <ul id="side-items"></ul>
      </section>
      </section>

      <section>
        <h4>今日の収入 / 支出</h4>
        <ul id="side-accounts"></ul>

        <!-- 今日の± -->
        <div class="side-net">
          今日の収支：<span id="side-net-day">-</span>
        </div>

        <!-- 今月合計± -->
        <div class="side-net-month">
          今月の収支：
          <span id="side-net-month" style="color: {{ $netMonth >= 0 ? '#118a2a' : '#c21c1c' }};">
            ¥{{ number_format($netMonth) }}
          </span>
        </div>
      </section>
    </aside>

  </div>

  <script>
    // ==========================
    // Date変換（入れ子なし / JST固定で安定）
    // "YYYY-MM-DD HH:mm:ss" → Date
    // ==========================
    function toDateJST(dt) {
      return new Date(dt.replace(' ', 'T') + '+09:00');
    }

    document.addEventListener('DOMContentLoaded', function() {

      const itemCounts = @json($itemCounts);
      const incomeSums = @json($incomeSums);
      const expenseSums = @json($expenseSums);
      const items = @json($items);
      const accounts = @json($accounts);

      let clickTimer = null;
      let clickedDate = null;
      let isFirstRender = true;

      // ==========================
      // サイドパネル：2クリックでG05へ
      // ==========================
      const sidePanelEl = document.getElementById('side-panel');
      let sideClickTimer = null;
      let sideClickedDate = null;

      sidePanelEl.addEventListener('click', function() {
        const selectedDate = sidePanelEl.dataset.date;
        if (!selectedDate) return;

        if (sideClickedDate === selectedDate && sideClickTimer) {
          clearTimeout(sideClickTimer);
          sideClickedDate = null;
          window.location.href = `/calendar/events/${selectedDate}`;
          return;
        }

        sideClickedDate = selectedDate;
        sideClickTimer = setTimeout(() => {
          sideClickedDate = null;
        }, 300);
      });

      // ==========================
      // 祝日データ取得 → カレンダー生成
      // ==========================
      fetch('https://holidays-jp.github.io/api/v1/date.json')
        .then(res => res.json())
        .then(holidays => {

          const calendar = new FullCalendar.Calendar(
            document.getElementById('calendar'),
            {
              initialView: 'dayGridMonth',
              locale: 'ja',
              timeZone: 'local',
              height: 'auto',
              fixedWeekCount: false,
              initialDate: "{{ $baseDate->format('Y-m-d') }}",

              headerToolbar: {
                left: 'prev',
                center: 'title',
                right: 'next'
              },

              datesSet: function(info) {
                if (isFirstRender) {
                  isFirstRender = false;
                  return;
                }
                const mid = new Date(info.start.getTime() + (info.end - info.start) / 2);
                const y = mid.getFullYear();
                const m = String(mid.getMonth() + 1).padStart(2, '0');
                window.location.href = `/calendar?date=${y}-${m}-01`;
              },

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
                  showSidePanel(dateStr, items, accounts);
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

                // 日付 + 祝日 横並び
                const wrapper = document.createElement('div');
                wrapper.className = 'fc-day-label';

                const numberEl = top.querySelector('.fc-daygrid-day-number');
                if (numberEl) wrapper.appendChild(numberEl);

                if (holidays[dateStr]) {
                  info.el.classList.add('fc-day-holiday');
                  const holidayEl = document.createElement('span');
                  holidayEl.className = 'fc-holiday-label';
                  holidayEl.textContent = holidays[dateStr];
                  wrapper.appendChild(holidayEl);
                }

                top.innerHTML = '';
                top.appendChild(wrapper);

                // サマリー
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
            }
          );

          calendar.render();
        });

      // ==========================
      // 右サイド表示（終日・時間指定・期間指定すべて拾う完成形）
      // ==========================
      function showSidePanel(dateStr, items, accounts) {
        // パネルに日付保持
        const sidePanel = document.getElementById('side-panel');
        sidePanel.dataset.date = dateStr;
        document.getElementById('selected-date').textContent = dateStr;

        // --- 予定 ---
        const itemList = document.getElementById('side-items');
        itemList.innerHTML = '';

        const dayStart = new Date(dateStr + 'T00:00:00+09:00');
        const dayEnd   = new Date(dateStr + 'T23:59:59+09:00');

      const todayItems = items
        .filter(i => {
          // 判定に使う開始・終了を統一
          // 予定: sche_start/sche_end
          // タスク: sche_done を開始・終了扱い（1日）
          const startStr = i.sche_start || i.sche_done;
          if (!startStr) return false;

          const start = toDateJST(startStr);
          if (isNaN(start.getTime())) return false;

          const endStr = i.sche_end || startStr;
          const end = toDateJST(endStr);
          if (isNaN(end.getTime())) return false;

          // 当日に少しでも重なれば表示
          return start <= dayEnd && end >= dayStart;
        })
        .sort((a, b) => {
          const aKey = toDateJST(a.sche_start || a.sche_done);
          const bKey = toDateJST(b.sche_start || b.sche_done);
          return aKey - bKey;
        });



        if (todayItems.length === 0) {
          itemList.innerHTML = '<li>予定はありません</li>';
        } else {
            todayItems.forEach(i => {
            const isSchedule = (i.subcategory_id == 1);
            const isTask     = !isSchedule;

            // 種別
            const type = isSchedule ? '予定' : 'タスク';

            // 時刻（予定は開始、タスクは期限）
            // 文字列が "YYYY-MM-DD HH:MM:SS" でも "YYYY-MM-DDTHH:MM:SS..." でも対応
            const dtStr = isSchedule ? i.sche_start : i.sche_done;
            const time = dtStr
              ? dtStr.replace('T', ' ').slice(11, 16)
              : '';

            // 終日（type_id==2 の予定なら "終日" を優先表示）
            const timeLabel = (isSchedule && i.type_id == 2)
              ? '終日'
              : time;

            // 完了ステータス（タスクだけ）
            const status = isTask
              ? (i.status_id == 2 ? '済' : '未')   // 2を済、それ以外を未に寄せると事故りにくい
              : '';

            const li = document.createElement('li');

            const statusPart = status ? `[${status}] ` : '';
            const timePart = timeLabel ? `${timeLabel} ` : '';

            li.textContent = `【${type}】${statusPart}${timePart}${i.title}`;
            itemList.appendChild(li);
          });
        }



        // --- 収支 ---
        const accList = document.getElementById('side-accounts');
        accList.innerHTML = '';

        const todayAcc = accounts.filter(a => {
          const d = new Date(a.date);
          const ds = `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
          return ds === dateStr;
        });

        if (todayAcc.length === 0) {
          accList.innerHTML = '<li>収支の登録はありません</li>';
        } else {
          todayAcc.forEach(a => {
            const isExpense = a.subcategory_id == 4;
            const label = isExpense ? '支出' : '収入';
            const sign = isExpense ? '-' : '+';

            const li = document.createElement('li');
            li.classList.add(isExpense ? 'expense' : 'income');
            li.textContent = `${label} ${sign}¥${Number(a.amount).toLocaleString()} ${a.title ?? ''}`;
            accList.appendChild(li);
          });
        }

        // 今日の収支（±）
        let incomeDay = 0;
        let expenseDay = 0;

        todayAcc.forEach(a => {
          if (a.subcategory_id == 3) incomeDay += Number(a.amount);
          if (a.subcategory_id == 4) expenseDay += Number(a.amount);
        });

        const netDay = incomeDay - expenseDay;
        const netEl = document.getElementById('side-net-day');
        netEl.textContent = `¥${netDay.toLocaleString()}`;
        netEl.style.color = netDay >= 0 ? '#118a2a' : '#c21c1c';
      }

    });
  </script>

</body>
</html>
