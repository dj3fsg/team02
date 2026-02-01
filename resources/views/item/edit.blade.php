<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>カレンダー編集画面</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  @include('parts.head')

  <style>
    .form-wrap {
      max-width: 600px; 
      margin: 0 auto;
    }
  </style>
</head>

<body>
  @include('parts.header')

  <div class="p-3 pb-2 d-flex align-items-center justify-content-center bg-info-subtle">
    <h1 class="h2">予定編集</h1>
  </div>

  <div class="container-sm p-3">
    <div class="form-wrap bg-white p-4 rounded-3 shadow-sm">

      <form id="update-form"
            method="POST"
            action="{{ url('calendar/update/' . $item->id) }}"
            onsubmit="return confirm('この予定を更新してもよろしいですか？');">
        @csrf
        @method('PUT')

        <!-- hidden（sche_start / sche_end を確定させる） -->
        <input type="hidden" name="sche_start" id="sche_start">
        <input type="hidden" name="sche_end"   id="sche_end">

        <!-- タイトル -->
        <div class="mb-3">
          <label class="form-label">タイトル</label>
          <input type="text" name="title" class="form-control"
                 value="{{ old('title', $item->title) }}">
        </div>

        <!-- 終日 -->
        <div class="mb-2 form-check">
          <input type="checkbox" id="chk_all_day" name="all_day" class="form-check-input"
            {{ old('all_day', $item->type_id == 2) ? 'checked' : '' }}>
          <label class="form-check-label" for="chk_all_day">終日の予定</label>
        </div>

        <!-- 日時 -->
        <div class="mb-3">
          <label class="form-label">日時</label>

          <!-- 終日ON -->
          <div id="all_day_on" class="{{ $item->type_id == 2 ? '' : 'd-none' }}">
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <input type="date" name="sche_start_date" class="form-control" style="max-width:200px"
                     value="{{ old('sche_start_date', \Carbon\Carbon::parse($item->sche_start)->format('Y-m-d')) }}">
              <span>〜</span>
              <input type="date" name="sche_end_date" class="form-control" style="max-width:200px"
                     value="{{ old('sche_end_date', $item->sche_end ? \Carbon\Carbon::parse($item->sche_end)->format('Y-m-d') : '') }}">
            </div>
          </div>

          <!-- 終日OFF -->
          <div id="all_day_off" class="{{ $item->type_id == 2 ? 'd-none' : '' }}">
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <input type="date" name="sche_start_date" class="form-control" style="max-width:200px"
                     value="{{ old('sche_start_date', \Carbon\Carbon::parse($item->sche_start)->format('Y-m-d')) }}">
              <input type="time" name="sche_start_time" class="form-control" style="max-width:150px"
                     value="{{ old('sche_start_time', \Carbon\Carbon::parse($item->sche_start)->format('H:i')) }}">
              <span>〜</span>
              <input type="time" name="sche_end_time" class="form-control" style="max-width:150px"
                     value="{{ old('sche_end_time', $item->sche_end ? \Carbon\Carbon::parse($item->sche_end)->format('H:i') : '') }}">
            </div>
          </div>
        </div>

        <!-- 場所 -->
        <div class="mb-3">
          <label class="form-label">場所</label>
          <input type="text" name="location" class="form-control"
                 value="{{ old('location', $item->location) }}">
        </div>

        <!-- 備考 -->
        <div class="mb-3">
          <label class="form-label">備考</label>
          <textarea name="memo" class="form-control" rows="4">{{ old('memo', $item->memo) }}</textarea>
        </div>

        <!-- 完了 -->
        <div class="mb-3 form-check">
          <input type="checkbox" name="status_id" value="2" class="form-check-input" id="status_done"
            {{ old('status_id', $item->status_id) == 2 ? 'checked' : '' }}>
          <label class="form-check-label" for="status_done">完了にする</label>
        </div>

        <!-- ボタン（並びはそのまま） -->
        <div class="mt-4 d-flex gap-2">
          <a href="{{ url('calendar') }}" class="btn btn-outline-secondary">戻る</a>
          <button type="button" class="btn btn-danger" onclick="deleteItem()">削除</button>
          <button type="submit" class="btn btn-primary">更新</button>
        </div>

      </form>
    </div>
  </div>

  <form id="delete-form"
        method="POST"
        action="{{ url('calendar/delete/' . $item->id) }}"
        style="display:none;">
    @csrf
    @method('DELETE')
  </form>

  <!-- sche_start / sche_end を確定 -->
  <script>
  document.getElementById('update-form').addEventListener('submit', function () {

    const isAllDay = document.getElementById('chk_all_day').checked;

    const startDate = document.querySelector(
      '#all_day_on:not(.d-none) [name="sche_start_date"], #all_day_off:not(.d-none) [name="sche_start_date"]'
    ).value;

    const startTime = document.querySelector(
      '#all_day_off:not(.d-none) [name="sche_start_time"]'
    )?.value || '00:00:00';

    const endDate = document.querySelector(
      '#all_day_on:not(.d-none) [name="sche_end_date"]'
    )?.value || startDate;

    const endTime = document.querySelector(
      '#all_day_off:not(.d-none) [name="sche_end_time"]'
    )?.value || '23:59:59';

    document.getElementById('sche_start').value =
      isAllDay ? `${startDate} 00:00:00` : `${startDate} ${startTime}`;

    document.getElementById('sche_end').value =
      isAllDay ? `${endDate} 23:59:59` : `${startDate} ${endTime}`;
  });

  document.addEventListener('DOMContentLoaded', function () {

    const chkAllDay = document.getElementById('chk_all_day');
    const allDayOn  = document.getElementById('all_day_on');
    const allDayOff = document.getElementById('all_day_off');

    function toggleAllDay() {
      if (chkAllDay.checked) {
        allDayOn.classList.remove('d-none');
        allDayOff.classList.add('d-none');
      } else {
        allDayOn.classList.add('d-none');
        allDayOff.classList.remove('d-none');
      }
    }

    toggleAllDay();
    chkAllDay.addEventListener('change', toggleAllDay);
  });

  function deleteItem() {
    if (confirm('この予定を削除してもよろしいですか？')) {
      document.getElementById('delete-form').submit();
    }
  }
  </script>
</body>
</html>
