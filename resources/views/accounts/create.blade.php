<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>収支作成</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f5f5f5;
      margin: 0;
      padding: 0;
    }
    .form-wrap { max-width: 520px; margin: 0 auto; }
    .form-label { margin-bottom: .35rem; }
  </style>

  @include('parts.head')
</head>

<body>
  @include('parts.header')

  <div class="p-3 pb-2 d-flex align-items-center justify-content-center bg-info-subtle">
    <h1 class="h2">収支作成</h1>
  </div>

  <div class="container-sm p-3">
    <div class="form-wrap bg-white p-4 rounded-3 shadow-sm">

      @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('accounts.store') }}">
        @csrf

        <!-- 日付 -->
        <div class="mb-3">
          <label class="form-label">日付</label>
          <input
            type="date"
            name="date"
            style="max-width: 150px;"
            class="form-control"
            value="{{ old('date', now()->format('Y-m-d')) }}"
          >
        </div>

        <!-- 繰り返し -->
        <div class="mb-3">
          <label class="form-label">繰り返し</label>
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <select name="repeat" id="repeat_id" class="form-select" style="max-width: 160px;">
              <option value="0" {{ old('repeat', '0') === '0' ? 'selected' : '' }}>無し</option>
              <option value="1" {{ old('repeat') === '1' ? 'selected' : '' }}>毎週</option>
              <option value="2" {{ old('repeat') === '2' ? 'selected' : '' }}>毎月</option>
              <option value="3" {{ old('repeat') === '3' ? 'selected' : '' }}>毎年</option>
            </select>

            <label for="repeat_until" class="form-label mb-0">
              繰り返し期限
            </label>

            <input
              type="date"
              id="repeat_until"
              name="repeat_until"
              class="form-control @error('repeat_until') is-invalid @enderror"
              style="max-width: 200px;"
              value="{{ old('repeat_until') }}"
              min="{{ old('date') }}"
              max="{{ old('date') ? \Carbon\Carbon::parse(old('date'))->addYears(2)->toDateString() : '' }}"
            >
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label d-block">区分</label>
          <div class="d-flex gap-4 flex-wrap">
            <div class="form-check">
              <input class="form-check-input"
                    type="radio"
                    name="subcategory_id"
                    id="subcat_in"
                    value="3"
                    {{ (string)old('subcategory_id') === '3' ? 'checked' : '' }}>
              <label class="form-check-label" for="subcat_in">入金</label>
          </div>

          <div class="form-check">
            <input class="form-check-input"
                  type="radio"
                  name="subcategory_id"
                  id="subcat_out"
                  value="4"
                  {{ (string)old('subcategory_id') === '4' ? 'checked' : '' }}>
            <label class="form-check-label" for="subcat_out">出金</label>
          </div>
        </div>
      </div>


        <!-- 金額 -->
        <div class="mb-3">
          <label class="form-label">金額</label>
          <input
            type="number"
            name="amount"
            class="form-control"
            style="max-width: 200px;"
            inputmode="numeric"
            value="{{ old('amount') }}"
          >
        </div>

        <!-- タイトル -->
        <div class="mb-3">
          <label class="form-label">タイトル</label>
          <input
            type="text"
            name="title"
            style="max-width: 420px;"
            class="form-control"
            value="{{ old('title') }}"
          >
        </div>
      <label class="form-label">カテゴリ</label>
       <select
        name="account_category_id"
        id="account_category_id"
        class="form-select"
        style="max-width: 200px;"
        data-old="{{ old('account_category_id') }}"
      >
        <option value="">選択してください</option>

        <optgroup label="入金">
          @foreach($incomeCategories as $cat)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
          @endforeach
        </optgroup>

        <optgroup label="出金">
          @foreach($expenseCategories as $cat)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
          @endforeach
        </optgroup>
      </select>



        <!-- メモ -->
        <div class="mb-4">
          <label class="form-label">メモ</label>
          <input
            type="text"
            name="memo"
            rows="4"
            maxlength="255"
            class="form-control"
            value="{{ old('memo') }}"
          >
        </div>

        <!-- ボタン -->
        <div class="col text-start">
          <a href="{{ url('calendar') }}" class="btn btn-outline-secondary">戻る</a>
          <button type="submit" class="btn btn-primary">登録</button>
        </div>
      </form>

    </div>
  </div>
 <script src="{{ asset('js/account.js') }}"></script>
</body>
</html>
