<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>予定作成</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @include('parts.head')
</head>

<body>
    @include('parts.header')

    <div class="p-3 pb-2 d-flex align-items-center justify-content-center bg-info-subtle">
        <h1 class="h2">予定作成</h1>
    </div>

    <div class="container p-3">
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="/calendar">
            @csrf

            <div class="row mt-4 mb-4 align-items-center">
                <div class="col text-start">
                    <a href="{{ url('calendar') }}" class="btn btn-outline-secondary">戻る</a>
                </div>
                <div class="col text-center">
                </div>
                <div class="col text-end">
                    <button type="submit" class="btn btn-primary">登録</button>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">種別</label><br>
                <label class="me-3">
                    <input type="radio" name="type" value="schedule" id="type_schedule"
                        {{ old('type', 'schedule') === 'schedule' ? 'checked' : '' }}>
                    スケジュール
                </label>
                <label>
                    <input type="radio" name="type" value="task" id="type_task"
                        {{ old('type') === 'task' ? 'checked' : '' }}>
                    タスク
                </label>
            </div>

            <div class="mb-3">
                <label for="title" class="form-label">タイトル</label>
                <input type="text" id="title" name="title" class="form-control"
                    value="{{ old('title') }}" placeholder="例：打合せ">
            </div>

            <div id="schedule_area">
                <div class="mb-2">
                    <label>
                        <input type="checkbox" id="chk_all_day" name="all_day" {{ old('all_day') ? 'checked' : '' }}>
                        終日の予定
                    </label>
                </div>

                <div id="all_day_on" class="d-none mb-3">
                    <label class="form-label">日時</label>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <input type="date" name="sche_start_date" class="form-control" style="max-width: 200px;" value="{{ old('sche_start_date') }}">
                        <span>〜</span>
                        <input type="date" name="sche_end_date" class="form-control" style="max-width: 200px;" value="{{ old('sche_end_date') }}">
                    </div>
                </div>

                <div id="all_day_off" class="mb-3">
                    <label class="form-label">日時</label>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <input type="date" name="sche_start_date" class="form-control" style="max-width: 200px;" value="{{ old('sche_start_date') }}">
                        <input type="time" name="sche_start_time" class="form-control" style="max-width: 150px;" value="{{ old('sche_start_time') }}">
                        <span>〜</span>
                        <input type="time" name="sche_end_time" class="form-control" style="max-width: 150px;" value="{{ old('sche_end_time') }}">
                    </div>
                </div>
            </div>

            <div id="task_area" class="d-none mb-3">
                <label class="form-label">期限</label>
                <div class="d-flex gap-2">
                    <input type="datetime-local" name="sche_done" class="form-control" style="max-width: 300px;" value="{{ old('task_due_at') }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">繰り返し</label>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <select name="repeat" id="repeat_id" class="form-select" style="max-width: 160px;">
                        <option value="0" {{ old('repeat', '0') === '0' ? 'selected' : '' }}>無し</option>
                        <option value="1" {{ old('repeat') === '1' ? 'selected' : '' }}>毎週</option>
                        <option value="2" {{ old('repeat') === '2' ? 'selected' : '' }}>毎月</option>
                        <option value="3" {{ old('repeat') === '3' ? 'selected' : '' }}>毎年</option>
                    </select>
                    <label class="form-label mb-0">繰り返し期限</label>
                    <input type="date" id="repeat_until" name="repeat_until" class="form-control" style="max-width: 200px;" value="{{ old('repeat_until') }}">
                </div>
            </div>

            <div class="mb-3">
                <label for="location" class="form-label">場所</label>
                <input type="text" id="location" name="location" class="form-control" value="{{ old('location') }}" placeholder="例：会議室A">
            </div>

            <div class="mb-3">
                <label for="memo" class="form-label">メモ</label>
                <textarea id="memo" name="memo" class="form-control" rows="4" placeholder="補足など">{{ old('memo') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-check-label">
                    <input type="checkbox" name="status_id" value="2" class="form-check-input" {{ old('status_id') == 2 ? 'checked' : '' }}> 完了にする
                </label>
            </div>
        </form>
    </div>
    <script src="{{ asset('js/calendar.js') }}"></script>
</body>

</html>