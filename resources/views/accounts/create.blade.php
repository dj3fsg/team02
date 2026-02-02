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

    /* フォーム全体の幅を締める */
    .form-wrap {
      max-width: 520px;
      margin: 0 auto;
    }

    /* Bootstrapの余白が強すぎる時の微調整（任意） */
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
          <input type="date" name="date" style="max-width: 150px;"　required class="form-control">
        </div>

        <!-- 区分 -->
        <div class="mb-3">
          <label class="form-label">区分</label>
          <select name="subcategory_id" required class="form-select">
            <option value="0">選択してください</option>
            <option value="3">収入</option>
            <option value="4">支出</option>
          </select>
        </div>

        <!-- 金額 -->
        <div class="mb-3">
          <label class="form-label">金額</label>
          <input type="number" name="amount" required class="form-control" inputmode="numeric">
        </div>

        <!-- タイトル -->
        <div class="mb-3">
          <label class="form-label">タイトル</label>
          <input type="text" name="title" class="form-control">
        </div>

        <!-- カテゴリ -->
        <div class="mb-3">
          <label class="form-label">カテゴリ</label>
          <select name="account_category_id" class="form-select">
            <option value="">選択してください</option>
            <option value="1">食費</option>
            <option value="2">日用品</option>
            <option value="3">交通費</option>
            <option value="4">家賃</option>
            <option value="5">娯楽</option>
            <option value="6">給料</option>
            <option value="9">その他</option>
          </select>
        </div>

        <!-- メモ -->
        <div class="mb-4">
          <label class="form-label">メモ</label>
          <input type="text" name="memo" class="form-control">
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
