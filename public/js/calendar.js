document.addEventListener('DOMContentLoaded', function () {
  console.log('calendar.js loaded');

  // --- 共通ツール ---
  function setDisabled(container, disabled) {
    if (!container) return;
    container.querySelectorAll('input, select, textarea, button').forEach(el => {
      el.disabled = disabled;
    });
  }

  // --- 要素取得 ---
  const typeSchedule = document.getElementById('type_schedule');
  const typeTask     = document.getElementById('type_task');
  const scheduleArea = document.getElementById('schedule_area');
  const taskArea     = document.getElementById('task_area');

  const chkAllDay = document.getElementById('chk_all_day');
  const allDayOn  = document.getElementById('all_day_on');
  const allDayOff = document.getElementById('all_day_off');

  const repeatId    = document.getElementById('repeat_id');
  const repeatUntil = document.getElementById('repeat_until');

  // 完了ブロック関連（編集画面）
  const statusBlock    = document.getElementById('statusBlock');
  const statusCheckbox = document.getElementById('status_done');
  const subcatHidden   = document.getElementById('subcategory_id'); // Bladeでhidden追加したやつ

  // --- 終日切り替え ---
  function toggleAllDay() {
    if (!chkAllDay || !allDayOn || !allDayOff) return;
    const isAllDay = chkAllDay.checked;

    allDayOn.classList.toggle('d-none', !isAllDay);
    allDayOff.classList.toggle('d-none', isAllDay);

    setDisabled(allDayOn, !isAllDay);
    setDisabled(allDayOff, isAllDay);
  }

  // --- 種別切り替え ---
  function toggleType() {
    if (!typeSchedule || !typeTask || !scheduleArea || !taskArea) return;

    const isTask = typeTask.checked;

    scheduleArea.classList.toggle('d-none', isTask);
    taskArea.classList.toggle('d-none', !isTask);

    setDisabled(scheduleArea, isTask);
    setDisabled(taskArea, !isTask);

    // スケジュール表示側の時だけ終日切り替えを反映
    if (!isTask) toggleAllDay();
  }

  // --- 繰り返し期限の制御 ---
  function toggleRepeatUntil() {
    if (!repeatId || !repeatUntil) return;
    const isNone = (repeatId.value === "0" || repeatId.value === "");
    repeatUntil.disabled = isNone;
    repeatUntil.style.backgroundColor = isNone ? "#e9ecef" : "#fff";
    if (isNone) repeatUntil.value = "";
  }

  // --- 完了（status_id）の表示制御：subcategory_id=1(スケジュール)なら隠す ---
  function syncStatusVisibility() {
    // この画面に完了ブロックが無い場合もあるので return で全体を止めない
    if (!statusBlock || !statusCheckbox || !subcatHidden) return;
