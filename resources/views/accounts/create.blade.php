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

        <!-- 区分 -->
        <div class="mb-3">
          <label class="form-label">区分</label>
          <select name="subcategory_id"  class="form-select">
            <option value="" {{ old('subcategory_id') === null || old('subcategory_id') === '' ? 'selected' : '' }}>
              選択してください
            </option>
            <option value="3" {{ old('subcategory_id') == '3' ? 'selected' : '' }}>収入</option>
            <option value="4" {{ old('subcategory_id') == '4' ? 'selected' : '' }}>支出</option>
          </select>
        </div>

        <!-- 金額 -->
        <div class="mb-3">
          <label class="form-label">金額</label>
          <input
            type="number"
            name="amount"
            class="form-control"
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
            class="form-control"
            value="{{ old('title') }}"
          >
        </div>

        <!-- カテゴリ -->
        <div class="mb-3">
          <label class="form-label">カテゴリ</label>
          <select name="account_category_id" class="form-select">
            <option value="" {{ old('account_category_id') === null || old('account_category_id') === '' ? 'selected' : '' }}>
              選択してください
            </option>
            <option value="1" {{ old('account_category_id') == '1' ? 'selected' : '' }}>食費</option>
            <option value="2" {{ old('account_category_id') == '2' ? 'selected' : '' }}>日用品</option>
            <option value="3" {{ old('account_category_id') == '3' ? 'selected' : '' }}>交通費</option>
            <option value="4" {{ old('account_category_id') == '4' ? 'selected' : '' }}>家賃</option>
            <option value="5" {{ old('account_category_id') == '5' ? 'selected' : '' }}>娯楽</option>
            <option value="6" {{ old('account_category_id') == '6' ? 'selected' : '' }}>給料</option>
            <option value="9" {{ old('account_category_id') == '9' ? 'selected' : '' }}>その他</option>
          </select>
        </div>

        <!-- メモ -->
        <div class="mb-4">
          <label class="form-label">メモ</label>
          <input
            type="text"
            name="memo"
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
</body>
</html>
