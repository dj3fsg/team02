@extends('layouts.common')
@section('content')

@if (Auth::id() == $account->user_id)

<div class="p-3 pb-2 d-flex align-items-center justify-content-center bg-info-subtle">
  <h1 class="h2">収支編集</h1>
</div>

<div class="container-sm p-3">
  <div class="mx-auto bg-white p-4 rounded-3 shadow-sm" style="max-width: 520px;">

    <form id="update-form"
          method="POST"
          action="{{ url('account/update/' . $account->id) }}"
          onsubmit="return confirm('この収支を更新してもよろしいですか？');">
      @csrf
      @method('PUT')

      <!-- 日付 -->
      <div class="mb-3">
        <label class="form-label">日付</label>
        <input type="date"
               name="date"
               value="{{ $account->date?->format('Y-m-d') }}"
               style="max-width: 150px;"
               class="form-control"
               required>
      </div>

      <!-- 区分 -->
      <div class="mb-3">
        <label class="form-label d-block">区分</label>
        <div class="d-flex gap-4 flex-wrap">
          <div class="form-check">
            <input class="form-check-input"
                   type="radio"
                   name="subcategory_id"
                   id="subcat_in"
                   value="3"
                   {{ $account->subcategory_id == 3 ? 'checked' : '' }}>
            <label class="form-check-label" for="subcat_in">入金</label>
          </div>

          <div class="form-check">
            <input class="form-check-input"
                   type="radio"
                   name="subcategory_id"
                   id="subcat_out"
                   value="4"
                   {{ $account->subcategory_id == 4 ? 'checked' : '' }}>
            <label class="form-check-label" for="subcat_out">出金</label>
          </div>
        </div>
      </div>

      <!-- 金額 -->
      <div class="mb-3">
        <label class="form-label">金額</label>
        <input type="number"
               name="amount"
               value="{{ (int) $account->amount }}"
               class="form-control"
               inputmode="numeric"
               required>
      </div>

      <!-- タイトル -->
      <div class="mb-3">
        <label class="form-label">タイトル</label>
        <input type="text"
               name="title"
               value="{{ $account->title }}"
               class="form-control">
      </div>

      <!-- カテゴリ -->
      <div class="mb-3">
        <label class="form-label">カテゴリ</label>
        <select name="account_category_id" class="form-select">
          <option value="0">選択してください</option>
          @foreach ($account_categories as $account_category)
            <option value="{{ $account_category->id }}"
                    {{ $account->account_category_id == $account_category->id ? 'selected' : '' }}>
              {{ $account_category->name }}
            </option>
          @endforeach
        </select>
      </div>

      <!-- メモ -->
      <div class="mb-4">
        <label class="form-label">メモ</label>
        <input type="text"
               name="memo"
               value="{{ $account->memo }}"
               class="form-control">
      </div>

      {{-- ボタン部分は変更しない（並び/役割はそのまま） --}}
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
      action="{{ url('account/delete/' . $account->id) }}"
      style="display:none;">
  @csrf
  @method('DELETE')
</form>

@else
  <h2>不正な閲覧</h2>
@endif

<script>
function deleteItem() {
  if (confirm('この収支を削除してもよろしいですか？')) {
    document.getElementById('delete-form').submit();
  }
}
</script>

@endsection
