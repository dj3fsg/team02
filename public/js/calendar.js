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

  // 編集画面の「完了」ブロック関連（存在する画面だけ）
  const statusBlock    = document.getElementById('statusBlock');
  const statusCheckbox = document.getElementById('status_done');
  const subcatHidden   = document.getElementById('subcategory_id'); // hidden

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

    // スケジュール表示中だけ終日切り替えを反映
    if (!isTask) toggleAllDay();
  }

  // --- 繰り返し期限の制御 ---
  function toggleRepeatUntil() {
    if (!repeatId || !repeatUntil) return;

    // 「0 (無し)」のときは非活性
    const isNone = (repeatId.value === '0' || repeatId.value === '');

    repeatUntil.disabled = isNone;
    repeatUntil.style.backgroundColor = isNone ? '#e9ecef' : '';
    if (isNone) repeatUntil.value = '';
  }

  // --- 完了（status）の表示制御：subcategory_id=1(スケジュール)なら隠す ---
  function syncStatusVisibility() {
    // この画面に完了ブロックが無い場合もあるので、何もしない
    if (!statusBlock || !statusCheckbox || !subcatHidden) return;

    const SCHEDULE_SUBCATEGORY_ID = '1'; // 1=スケジュール, 2=タスク
    const subcat = String(subcatHidden.value);

    if (subcat === SCHEDULE_SUBCATEGORY_ID) {
      statusBlock.style.display = 'none';
      statusCheckbox.checked = false; // 値を落とす
      statusCheckbox.disabled = true; // 送信させない
    } else {
      statusBlock.style.display = '';
      statusCheckbox.disabled = false;
    }
  }

  // --- イベント登録 ---
  if (typeSchedule) typeSchedule.addEventListener('change', toggleType);
  if (typeTask)     typeTask.addEventListener('change', toggleType);
  if (chkAllDay)    chkAllDay.addEventListener('change', toggleAllDay);
  if (repeatId)     repeatId.addEventListener('change', toggleRepeatUntil);

  // --- 初期実行 ---
  toggleType();
  toggleRepeatUntil();
  syncStatusVisibility();
});
