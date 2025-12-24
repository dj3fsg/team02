document.addEventListener('DOMContentLoaded', function () {
  // -----------------------------
  // Utility
  // -----------------------------
  function setDisabled(container, disabled) {
    if (!container) return;
    container.querySelectorAll('input, select, textarea, button').forEach(el => {
      // 切り替えロジックのボタンまで無効化したくない場合は button を外してOK
      el.disabled = disabled;
    });
  }

  function safeToggleClass(el, className, shouldHave) {
    if (!el) return;
    el.classList.toggle(className, shouldHave);
  }

  // -----------------------------
  // Elements (Type switch)
  // 期待する id:
  //  - type_schedule, type_task
  //  - schedule_area, task_area
  // -----------------------------
  const typeSchedule = document.getElementById('type_schedule');
  const typeTask = document.getElementById('type_task');
  const scheduleArea = document.getElementById('schedule_area');
  const taskArea = document.getElementById('task_area');

  // -----------------------------
  // Elements (All-day switch inside schedule)
  // 期待する id:
  //  - chk_all_day
  //  - all_day_on, all_day_off
  // -----------------------------
  const chkAllDay = document.getElementById('chk_all_day');
  const allDayOn = document.getElementById('all_day_on');
  const allDayOff = document.getElementById('all_day_off');

  // -----------------------------
  // Debug: 読み込み確認（必要なら残してOK）
  // -----------------------------
  // console.log('calendar.js loaded', {
  //   typeSchedule, typeTask, scheduleArea, taskArea,
  //   chkAllDay, allDayOn, allDayOff
  // });

  // -----------------------------
  // All-day toggle
  // -----------------------------
  function toggleAllDay() {
    // 要素が無い画面（タスクだけ等）でも落ちない
    if (!chkAllDay || !allDayOn || !allDayOff) return;

    const isAllDay = chkAllDay.checked;

    // 表示切替（Bootstrap d-none）
    safeToggleClass(allDayOn, 'd-none', !isAllDay);
    safeToggleClass(allDayOff, 'd-none', isAllDay);

    // 送信事故防止：表示している側だけ有効化
    setDisabled(allDayOn, !isAllDay);
    setDisabled(allDayOff, isAllDay);
  }

  // -----------------------------
  // Type toggle (schedule / task)
  // -----------------------------
  function toggleType() {
    // type が無い画面でも落ちない
    if (!typeSchedule || !typeTask || !scheduleArea || !taskArea) return;

    const isTask = typeTask.checked === true;

    // 表示切替
    safeToggleClass(scheduleArea, 'd-none', isTask);
    safeToggleClass(taskArea, 'd-none', !isTask);

    // 送信事故防止：表示している方だけ有効化
    setDisabled(scheduleArea, isTask);
    setDisabled(taskArea, !isTask);

    // スケジュール表示のときだけ終日判定を反映
    if (!isTask) toggleAllDay();
  }

  // -----------------------------
  // Event binding
  // -----------------------------
  if (typeSchedule && typeTask) {
    typeSchedule.addEventListener('change', toggleType);
    typeTask.addEventListener('change', toggleType);
  }

  if (chkAllDay) {
    chkAllDay.addEventListener('change', toggleAllDay);
  }

  // -----------------------------
  // Initial render (超重要)
  // -----------------------------
  toggleType();    // typeが無い画面なら何もしない
  toggleAllDay();  // all_dayが無い画面なら何もしない
});
