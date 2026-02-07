@extends('layouts.common')
@section('content')

@if (Auth::id() == $account->user_id)

<div class="p-3 pb-2 d-flex align-items-center justify-content-center bg-info-subtle">
  <h1 class="h2">収支編集</h1>
</div>

<div class="container-sm p-3">
  <div class="mx-auto bg-white p-4 rounded-3 shadow-sm" style="max-width: 520px;">

    {{-- =======================
         更新フォーム
         ======================= --}}
    <form id="update-form"
          method="POST"
          action="{{ url('account/update/' . $account->id) }}">
      @csrf
      @method('PUT')

      <!-- 日付 -->
      <div class="mb-3">
        <label class="form-label">日付</label>
        <input type="date"
               name="date"
               value="{{ old('date', $account->date?->format('Y-m-d')) }}"
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
        <input type="number"
               name="amount"
               value="{{ old('amount', (int)$account->amount) }}"
               class="form-control"
               inputmode="numeric"
               required>
      </div>

      <!-- タイトル -->
      <div class="mb-3">
        <label class="form-label">タイトル</label>
        <input type="text"
               name="title"
               value="{{ old('title', $account->title) }}"
               class="form-control">
      </div>

      <!-- カテゴリ -->
      <div class="mb-3">
        <label class="form-label">カテゴリ</label>
        <select name="account_category_id" class="form-select">
          <option value="0">選択してください</option>
          @foreach ($account_categories as $account_category)
            <option value="{{ $account_category->id }}"
              {{ (string)old('account_category_id', $account->account_category_id) === (string)$account_category->id ? 'selected' : '' }}>
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
               value="{{ old('memo', $account->memo) }}"
               class="form-control">
      </div>

      <!-- 更新ボタン群 -->
      <div class="mt-4 d-flex flex-wrap gap-2">
        <a href="{{ url('calendar') }}" class="btn btn-outline-secondary">戻る</a>

        {{-- シリーズ無し/有り共通：この収支のみ更新 --}}
        <button type="submit"
                name="scope"
                value="single"
                class="btn btn-primary"
                onclick="return confirm('この収支のみ更新してもよろしいですか？');">
          この収支のみ更新
        </button>

        {{-- シリーズ有りのみ：これ以降 / すべて --}}
        @if(!empty($account->repeats_id))
          <button type="submit"
                  name="scope"
                  value="future"
                  class="btn btn-warning"
                  onclick="return confirm('これ以降の収支を更新してもよろしいですか？');">
            これ以降を更新
          </button>

          <button type="submit"
                  name="scope"
                  value="all"
                  class="btn btn-danger"
                  onclick="return confirm('すべての収支を更新してもよろしいですか？');">
            すべてを更新
          </button>
        @endif
      </div>

    </form>

    {{-- =======================
         削除ボタン群（別フォーム）
         ======================= --}}
    <div class="mt-4 border-top pt-3">
      <div class="d-flex flex-wrap gap-2">

        {{-- シリーズ無し/有り共通：この収支のみ削除 --}}
        <form method="POST" action="{{ url('account/delete/' . $account->id) }}">
          @csrf
          @method('DELETE')
          <button type="submit"
                  name="scope"
                  value="single"
                  class="btn btn-outline-danger"
                  onclick="return confirm('この収支のみ削除してもよろしいですか？');">
            この収支のみ削除
          </button>
        </form>

        {{-- シリーズ有りのみ：これ以降 / すべて削除 --}}
        @if(!empty($account->repeats_id))
          <form method="POST" action="{{ url('account/delete/' . $account->id) }}">
            @csrf
            @method('DELETE')
            <button type="submit"
                    name="scope"
                    value="future"
                    class="btn btn-outline-warning"
                    onclick="return confirm('これ以降の収支を削除してもよろしいですか？');">
              これ以降を削除
            </button>
          </form>

          <form method="POST" action="{{ url('account/delete/' . $account->id) }}">
            @csrf
            @method('DELETE')
            <button type="submit"
                    name="scope"
                    value="all"
                    class="btn btn-outline-dark"
                    onclick="return confirm('すべての収支を削除してもよろしいですか？');">
              すべてを削除
            </button>
          </form>
        @endif

      </div>
    </div>

  </div>
</div>

@else
  <h2>不正な閲覧</h2>
@endif

@endsection
