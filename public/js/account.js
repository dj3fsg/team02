
document.addEventListener('DOMContentLoaded', function () {
  const repeatSelect = document.getElementById('repeat_id');
  const repeatUntil  = document.getElementById('repeat_until');

  function toggleRepeatUntil() {
    if (repeatSelect.value === '0') {
      repeatUntil.disabled = true;
      repeatUntil.value = ''; // 任意：値も消すなら
    } else {
      repeatUntil.disabled = false;
    }
  }

  // 初期表示時
  toggleRepeatUntil();

  // 変更時
  repeatSelect.addEventListener('change', toggleRepeatUntil);
});
