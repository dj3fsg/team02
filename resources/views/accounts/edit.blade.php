@extends('layouts.common')
@section('content')

<!-- <h1>title : {{ $account->title }}</h1>
<h2>id : {{ $account->id }}</h2>
<h2>user_id : {{ $account->user_id }}</h2>
<h2>subcategory_id : {{ $account->subcategory_id }}</h2>
<h2>type_id : {{ $account->type_id }}</h2>
<h2>status_id : {{ $account->status_id }}</h2>
<h2>date : {{ $account->date }}</h2>
<h2>amount : {{ $account->amount }}</h2>
<h2>memo : {{ $account->memo }}</h2>
<h2>created_at : {{ $account->created_at }}</h2>
<h2>updated_at : {{ $account->updated_at }}</h2> -->
@if (Auth::id() == $account->user_id)

<div class="p-3 pb-2 d-flex align-items-center justify-content-center bg-info-subtle">
    <h1 class="h2">収支編集</h1>
</div>

<div class="p-3 d-flex justify-content-center">
    <form method="POST" action="{{ route('update', $account->id) }}">
        @csrf
        <table class="table">
            <tr>
                <th style="width: 40%"></th>
                <th style="width: 60%"></th>
            </tr>
            <tr>
                <td><span style="margin-left: 2em;">日付</span></td>
                <td><input class="input" type="date" name="date" value="{{ $account->date?->format('Y-m-d') }}" style="width: 100%; padding: 0;"></td>
            </tr>
            <tr>
                <td><span style="margin-left: 2em;">区分</span></td>
                <td>
                    <label><span style="margin-left: 2em;margin-right: 2em;"><input type="radio" name="subcategory_id" value="3" {{ $account->subcategory_id == 3 ? 'checked' : '' }}>入金</span></label>
                    <label><span style="margin-left: 2em;margin-right: 2em;"><input type="radio" name="subcategory_id" value="4" {{ $account->subcategory_id == 4 ? 'checked' : '' }}>出金</span></label>
                </td>
            </tr>
            <tr>
                <td><span style="margin-left: 2em;">金額</span></td>
                <td><input class="input" type="text" name="amount" value="{{ number_format(floor($account->amount), 0) }}" style="width: 100%; padding: 0;"></td>
            </tr>
            <tr>
                <td><span style="margin-left: 2em;">タイトル</span></td>
                <td><input class="input" type="text" name="title" value="{{ $account->title }}" style="width: 100%; padding: 0;"></td>
            </tr>
            <tr>
                <td><span style="margin-left: 2em;">カテゴリ</span></td>
                <td>
                    <!-- カテゴリ -->
                    <div class="form-group">
                        <select name="account_category_id" style="width: 100%; padding: 0;">
                            <option value="0">選択してください</option>
                            @foreach ($account_categories as $account_category)
                            <option value="{{$account_category->id}}" {{ $account->account_category_id == $account_category->id ? 'selected' : '' }}>{{$account_category->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </td>
            </tr>
            <tr>
                <td><span style="margin-left: 2em;">メモ</span></td>
                <td><input class="input" type="text" name="memo" value="{{ $account->memo }}" style="width: 100%; padding: 0;"></td>
            </tr>
        </table>

        <div class="mt-4">
            <!-- <div class="d-grid gap-2 d-md-block"> -->
            <button type="submit" class="btn btn-primary" style="width:150px" formaction="{!! route('update', $account->id) !!}">更新する</button>
            <button type="submit" class="btn btn-danger" style="width:150px" formaction="{!! route('delete', $account->id) !!}">削除する</button>
            <button type="button" class="btn btn-light" style="width:150px" onclick="history.back()">戻る</button>

        </div>
    </form>
</div>

@else
<h2>不正な閲覧</h2>
@endif

@endsection