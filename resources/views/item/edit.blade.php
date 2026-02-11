<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>カレンダー編集画面</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  @include('parts.head')

  <style>
    .form-wrap { max-width: 650px; margin: 0 auto; }
  </style>
</head>

<body>
  @include('parts.header')

  <div class="p-3 pb-2 d-flex align-items-center justify-content-center bg-info-subtle">
    <h1 class="h2">
      {{ $item->subcategory_id == 1 ? '予定編集' : 'タスク編集' }}
    </h1>
  </div>


  <div class="container-sm p-3">
    
  
    <div class="form-wrap bg-white p-4 rounded-3 shadow-sm">
      <div class="alert alert-warning small">
        <strong>注意：</strong><br>
          ・一括編集では日付{{ $item->subcategory_id == 1 ? '・時刻' : '' }}は変更されません。<br>
          ・終日予定、時間指定の切り替えは不可です。<br>
          ・入力内容は
          「この{{ $item->subcategory_id == 1 ? '予定' : 'タスク' }}のみ更新」
          ボタン押下時に確定されます。
      </div>
      @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
      {{-- =======================
           更新フォーム
           ======================= --}}
      <form id="update-form"
            method="POST"
            action="{{ url('calendar/update/' . $item->id) }}">
        @csrf
        @method('PUT')

        <!-- hidden（sche_start / sche_end を確定させる） -->
        <input type="hidden" name="sche_start" id="sche_start">
        <input type="hidden" name="sche_end"   id="sche_end">


        <!-- タイトル -->
        <div class="mb-3">
          <label class="form-label">タイトル</label>
          <input type="text" name="title" class="form-control"
                style="max-width: 420px;"
                maxlength="255"
                 value="{{ old('title', $item->title) }}">
        </div>
        @if ($item->subcategory_id == 1)


        <!-- 終日 -->
        <div class="mb-2 form-check">
          <input type="checkbox" id="chk_all_day" name="all_day" class="form-check-input"
                 {{ old('all_day', $item->type_id == 2) ? 'checked' : '' }} disabled>
          <label class="form-check-label" for="chk_all_day">終日の予定</label>
        </div>

        <!-- 日時 -->
        <div class="mb-3">
          <label class="form-label">日時</label>
          @if ($item->type_id == 2)

          <!-- 終日ON -->
          <div id="all_day_on">
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <input type="date"
                     name="sche_start_date"
                     class="form-control"
                     style="max-width:150px"
                     value="{{ old('sche_start_date', \Carbon\Carbon::parse($item->sche_start ?? now())->format('Y-m-d')) }}"
                     min="{{ now()->toDateString() }}">

              <span>〜</span>

              <input type="date"
                     name="sche_end_date"
                     class="form-control"
                     style="max-width:150px"
                     value="{{ old('sche_end_date', $item->sche_end ? \Carbon\Carbon::parse($item->sche_end)->format('Y-m-d') : '') }}">
            </div>
          </div>
          @else

          <!-- 終日OFF -->
          <div id="all_day_off">
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <input type="date"
                     name="sche_start_date"
                     class="form-control"
                     style="max-width:150px"
                     value="{{ old('sche_start_date', \Carbon\Carbon::parse($item->sche_start)->format('Y-m-d')) }}">

              <input type="time"
                     name="sche_start_time"
                     class="form-control"
                     style="max-width:100px"
                     value="{{ old('sche_start_time', \Carbon\Carbon::parse($item->sche_start)->format('H:i')) }}">

              <span>〜</span>

              <input type="time"' '
                     name="sche_end_time"
                     class="form-control"
                     style="max-width:100px"
                     value="{{ old('sche_end_time', $item->sche_end ? \Carbon\Carbon::parse($item->sche_end)->format('H:i') : '') }}">
            </div>
          </div>
           @endif
        </div>
        @else
         <div id="task_area" class="mb-3">
          <label class="form-label">期限</label>
          <div class="d-flex gap-2">
            <input type="datetime-local" name="sche_done" class="form-control" style="max-width: 200px;"
                   value="{{ old('sche_done',$item->sche_done) }}">
          </div>
        </div>
        @endif
       

        <!-- 場所 -->
        <div class="mb-3">
          <label class="form-label">場所</label>
          <input type="text" name="location" class="form-control"
                style="max-width:420px"
                maxlength="255"
                 value="{{ old('location', $item->location) }}">
        </div>

        <!-- メモ -->
        <div class="mb-3">
          <label class="form-label">メモ</label>
          <textarea name="memo" class="form-control" rows="4" maxlength="255">{{ old('memo', $item->memo) }}</textarea>
        </div>

        <input type="hidden" id="subcategory_id" value="{{ $item->subcategory_id }}">

        <!-- 完了 -->
        @if(old('subcategory_id', $item->subcategory_id) == 2)
          <div class="mb-3 form-check">
            <input type="checkbox" name="status_id" value="2" class="form-check-input" id="status_done"
                  {{ old('status_id', $item->status_id) == 2 ? 'checked' : '' }}>
            <label class="form-check-label" for="status_done">完了にする</label>
          </div>
        @endif

        @php
          $isSchedule = (int)old('subcategory_id', $item->subcategory_id) === 1;
          $label      = $isSchedule ? '予定' : 'タスク';
        @endphp

        <!-- 更新ボタン群（scopeをボタンで送る） -->
        <div class="mt-4 d-flex flex-wrap gap-2">
          <a href="{{ url('calendar') }}" class="btn btn-outline-secondary">戻る</a>

          {{-- シリーズ無し/有り 共通：単体更新 --}}
          <button type="submit"
                  name="scope"
                  value="single"
                  class="btn btn-primary"
                  onclick="return confirm('この{{ $label }}のみ更新してもよろしいですか？');">
            この{{ $label }}のみ更新
          </button>

          {{-- シリーズ有りのみ表示 --}}
          @if(!empty($item->repeats_id))
            <button type="submit"
                    name="scope"
                    value="future"
                    class="btn btn-warning"
                    onclick="return confirm('これ以降の{{ $label }}を更新してもよろしいですか？');">
              これ以降の{{ $label }}を更新
            </button>

            <button type="submit"
                    name="scope"
                    value="all"
                    class="btn btn-danger"
                    onclick="return confirm('すべての{{ $label }}を更新してもよろしいですか？');">
              すべての{{ $label }}を更新
            </button>
          @endif
        </div>
        </form>

        {{-- =======================
            削除フォーム（更新フォームと分離）
            ======================= --}}
        <div class="mt-4 border-top pt-3">
          <div class="d-flex flex-wrap gap-2">

            {{-- シリーズ無し/有り 共通：単体削除 --}}
            <form method="POST" action="{{ url('calendar/delete/' . $item->id) }}">
              @csrf
              @method('DELETE')
              <button type="submit"
                      name="scope"
                      value="single"
                      class="btn btn-outline-danger"
                      onclick="return confirm('この{{ $label }}のみ削除してもよろしいですか？');">
                この{{ $label }}のみ削除
              </button>
            </form>

            {{-- シリーズ有りのみ表示 --}}
            @if(!empty($item->repeats_id))
              <form method="POST" action="{{ url('calendar/delete/' . $item->id) }}">
                @csrf
                @method('DELETE')
                <button type="submit"
                        name="scope"
                        value="future"
                        class="btn btn-outline-warning"
                        onclick="return confirm('これ以降の{{ $label }}を削除してもよろしいですか？');">
                  これ以降の{{ $label }}を削除
                </button>
              </form>

              <form method="POST" action="{{ url('calendar/delete/' . $item->id) }}">
                @csrf
                @method('DELETE')
                <button type="submit"
                        name="scope"
                        value="all"
                        class="btn btn-outline-dark"
                        onclick="return confirm('すべての{{ $label }}を削除してもよろしいですか？');">
                  すべての{{ $label }}を削除
                </button>
              </form>
            @endif

          </div>
        </div>


    </div>
  </div>
   <script src="{{ asset('js/calendar.js') }}"></script>

</body>
</html>
