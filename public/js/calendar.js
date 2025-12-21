document.addEventListener('DOMContentLoaded', function () {
  // --- 共通ツール ---
  function setDisabled(container, disabled) {
    if (!container) return;
    container.querySelectorAll('input, select, textarea, button').forEach(el => {
      el.disabled = disabled;
    });
  }

  // --- 要素取得 ---
  const typeSchedule = document.getElementById('type_schedule');
  const typeTask = document.getElementById('type_task');
  const scheduleArea = document.getElementById('schedule_area');
  const taskArea = document.getElementById('task_area');
  const chkAllDay = document.getElementById('chk_all_day');
  const allDayOn = document.getElementById('all_day_on');
  const allDayOff = document.getElementById('all_day_off');
  const repeatId = document.getElementById('repeat_id');
  const repeatUntil = document.getElementById('repeat_until');

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
    if (!isTask) toggleAllDay();
  }

  // --- 繰り返し期限の制御 ---
  function toggleRepeatUntil() {
    if (!repeatId || !repeatUntil) return;
    // 「0 (無し)」のときは非活性
    const isNone = (repeatId.value === "0" || repeatId.value === "");
    repeatUntil.disabled = isNone;
    repeatUntil.style.backgroundColor = isNone ? "#e9ecef" : "#fff";
    if (isNone) repeatUntil.value = "";
  }

  // --- イベント登録 ---
  if (typeSchedule) typeSchedule.addEventListener('change', toggleType);
  if (typeTask) typeTask.addEventListener('change', toggleType);
  if (chkAllDay) chkAllDay.addEventListener('change', toggleAllDay);
  if (repeatId) repeatId.addEventListener('change', toggleRepeatUntil);

  // --- 初期実行 ---
  toggleType();
  toggleRepeatUntil();
});
