@extends('layouts.common')

@section('content')

@if (Auth::id() == $account->user_id)

  {{-- ヘッダー --}}
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

        {{-- 日付 --}}
        <div class="mb-3">
          <label class="form-label">日付</label>
          <input type="date"
                 name="date"
                 value="{{ old('date', $account->date?->format('Y-m-d')) }}"
                 class="form-control"
                 style="max-width: 150px;"
                 required>
        </div>

        {{-- 区分 --}}
        <div class="mb-3">
          <label class="form-label d-block">区分</label>

          @php
            $selectedSubcat = (int) old('subcategory_id', $account->subcategory_id ?? 0);
          @endphp

          <div class="d-flex gap-4 flex-wrap">
            <div class="form-check">
              <input class="form-check-input"
                     type="radio"
                     name="subcategory_id"
                     id="subcat_in"
                     value="3"
                     {{ $selectedSubcat === 3 ? 'checked' : '' }}>
              <label class="form-check-label" for="subcat_in">入金</label>
            </div>

            <div class="form-check">
              <input class="form-check-input"
                     type="radio"
                     name="subcategory_id"
                     id="subcat_out"
                     value="4"
                     {{ $selectedSubcat === 4 ? 'checked' : '' }}>
              <label class="form-check-label" for="subcat_out">出金</label>
            </div>
          </div>
        </div>

        {{-- 金額 --}}
        <div class="mb-3">
          <label class="form-label">金額</label>
          <input type="number"
                 name="amount"
                 value="{{ old('amount', (int) $account->amount) }}"
                 class="form-control"
                 inputmode="numeric"
                 required>
        </div>

        {{-- タイトル --}}
        <div class="mb-3">
          <label class="form-label">タイトル</label>
          <input type="text"
                 name="title"
                 value="{{ old('title', $account->title) }}"
                 class="form-control">
        </div>

        {{-- カテゴリ --}}
        @php
          $selectedCategory = (int) old('account_category_id', $account->account_category_id ?? 0);
        @endphp

        <div class="mb-3">
          <label class="form-label">カテゴリ</label>

          <select name="account_category_id"
                  id="account_category_id"
                  class="form-select"
                  data-old="{{ old('account_category_id', $account->account_category_id ?? '') }}"
                  data-income='@json($incomeCategories->map(fn($c) => ["id" => $c->id, "name" => $c->name])->values())'
                  data-expense='@json($expenseCategories->map(fn($c) => ["id" => $c->id, "name" => $c->name])->values())'>

            <option value="">選択してください</option>

            {{-- 初期表示は Blade で制御 --}}
            @if ($selectedSubcat === 3)
              @foreach ($incomeCategories as $cat)
                <option value="{{ $cat->id }}" {{ $cat->id === $selectedCategory ? 'selected' : '' }}>
                  {{ $cat->name }}
                </option>
              @endforeach
            @elseif ($selectedSubcat === 4)
              @foreach ($expenseCategories as $cat)
                <option value="{{ $cat->id }}" {{ $cat->id === $selectedCategory ? 'selected' : '' }}>
                  {{ $cat->name }}
                </option>
              @endforeach
            @endif

          </select>
        </div>

        {{-- メモ --}}
        <div class="mb-4">
          <label class="form-label">メモ</label>
          <input type="text"
                 name="memo"
                 value="{{ old('memo', $account->memo) }}"
                 class="form-control">
        </div>

        {{-- 更新ボタン --}}
        <div class="mt-4 d-flex flex-wrap gap-2">
          <a href="{{ url('calendar') }}" class="btn btn-outline-secondary">戻る</a>

          <button type="submit"
                  name="scope"
                  value="single"
                  class="btn btn-primary"
                  onclick="return confirm('この収支のみ更新してもよろしいですか？');">
            この収支のみ更新
          </button>

          @if (!empty($account->repeats_id))
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
           削除ボタン群
           ======================= --}}
      <div class="mt-4 border-top pt-3">
        <div class="d-flex flex-wrap gap-2">

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

          @if (!empty($account->repeats_id))
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

  <script src="{{ asset('js/account.js') }}"></script>

@else
  <h2>不正な閲覧</h2>
@endif

@endsection
