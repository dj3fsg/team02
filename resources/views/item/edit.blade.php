<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>カレンダー編集画面</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  @include('parts.head')
</head>
<body class="p-3">
  @include('parts.header')
  <div class="container">
    <form method="POST" action="{{ url('calendar/update/' . $item->id) }}" onsubmit="return confirm('この予定を更新してもよろしいですか？');">
      @csrf
      @method('PUT')
      <div class="mb-3">
        <label for="title" class="form-label">タイトル</label>
        <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" 
               value="{{ old('title', $item->title) }}" placeholder="例：打合せ">
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div id="schedule_area">

 <div class="mb-2">
  <label>
    <input type="checkbox" id="chk_all_day" name="all_day"
           {{ old('all_day', $item->type_id == 2) ? 'checked' : '' }}>
    終日の予定
  </label>
</div>

<div id="all_day_on" class="{{ old('all_day', $item->type_id == 2) ? '' : 'd-none' }} mb-3">
  <label class="form-label">日時</label>
  <div class="d-flex align-items-center gap-2">
    <input type="date" name="sche_start_date" class="form-control" 
           value="{{ old('sche_start_date', date('Y-m-d', strtotime($item->sche_start))) }}">
    <span>〜</span>
    <input type="date" name="sche_end_date" class="form-control" 
           value="{{ old('sche_end_date', $item->sche_end ? date('Y-m-d', strtotime($item->sche_end)) : '') }}">
  </div>
</div>

<div id="all_day_off" class="{{ old('all_day', $item->type_id == 2) ? 'd-none' : '' }} mb-3">
  <label class="form-label">日時</label>
  <div class="d-flex align-items-center gap-2">
    <input type="date" name="sche_start_date" class="form-control" 
           value="{{ old('sche_start_date', date('Y-m-d', strtotime($item->sche_start))) }}">
    <input type="time" name="sche_start_time" class="form-control" 
           value="{{ old('sche_start_time', date('H:i', strtotime($item->sche_start))) }}">
    <span>〜</span>
    <input type="time" name="sche_end_time" class="form-control" 
           value="{{ old('sche_end_time', $item->sche_end ? date('H:i', strtotime($item->sche_end)) : '') }}">
  </div>
</div>
</div>

      <div class="mb-3">
        <label for="location" class="form-label">場所</label>
        <input type="text" id="location" name="location" class="form-control" 
               value="{{ old('location', $item->location) }}" placeholder="例：会議室A">
      </div>

      <div class="mb-3">
        <label for="memo" class="form-label">メモ</label>
        <textarea id="memo" name="memo" class="form-control" rows="4" placeholder="補足など">{{ old('memo', $item->memo) }}</textarea>
      </div>

      <div class="mb-3">
  <label class="form-check-label">
    <input type="checkbox" name="status_id" value="2" class="form-check-input" 
           {{ old('status_id', $item->status_id) == 2 ? 'checked' : '' }}> 完了にする
  </label>
</div>

    </form>

    <form id="delete-form" method="POST" action="{{ url('calendar/delete/' . $item->id) }}" style="display: none;">
      @csrf
      @method('DELETE')
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function deleteItem() {
      if (confirm('この予定を削除してもよろしいですか？')) {
        document.getElementById('delete-form').submit();
      }
    }
  </script>
  <div class="row mt-4 mb-4 align-items-center">
        <div class="col text-start">
          <a href="{{ url('calendar') }}" class="btn btn-outline-secondary">戻る</a>
        </div>
        <div class="col text-center">
          <button type="button" class="btn btn-danger" onclick="deleteItem();">削除</button>
        </div>
        <div class="col text-end">
          <button type="submit" class="btn btn-primary">更新</button>
        </div>
      </div>
  <script src="{{ asset('js/calendar.js') }}"></script>
</body>
</html>