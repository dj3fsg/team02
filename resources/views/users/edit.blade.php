<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザ編集</title>

    <style>
        h2 {
            text-align: center;
        }

        .error-message {
            color: red;
            text-align: center;
            /* 中央寄せ */
            list-style: none;
            /* ・を消す */
            padding-left: 0;
            /* 左の余白を消す */
            margin-bottom: 20px;
        }

        .form-group {
            display: block;
            margin-bottom: 20px;
            margin: 0 auto;
            text-align: center;
        }


        .form-pass {
            margin-bottom: 10px;
            margin: 0 auto;
            text-align: center;
            font-size: 12px;
        }


        .form-label {
            display: block;
            font-weight: bold;
            margin-bottom: 2px;
            font-size: 20px;
        }

        .form-input {
            width: 20%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid black;
            box-sizing: border-box;
        }

        .form-inputA {
            width: 20%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid black;
            box-sizing: border-box;
            background-color: #dddfe08c;
            text-align: left;
        }

        .btn-blue {
            background-color: #007bff;
            /*更新ボタンの色*/
            color: white;
            border: none;
            padding: 11px 26px;
            /* 高さ・横幅 */
            font-size: 20px;
            /* 文字サイズ */
            border-radius: 6px;
            cursor: pointer;
        }

        .btn-blue:hover {
            background-color: #0062cc;
        }

        .btn-red {
            background-color: #e53935;
            /*削除ボタンの色*/
            color: white;
            border: none;
            padding: 11px 26px;
            /* 高さ・横幅*/
            font-size: 20px;
            /* 文字サイズ */
            border-radius: 6px;
            cursor: pointer;
        }

        .btn-red:hover {
            background-color: #c62828;
        }

        .p-group {
            text-align: center;
        }

        .role-group {
            width: 20%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid black;
            box-sizing: border-box;
            text-align: left;
        }

        /*権限プルダウン*/
    </style>

    <!-- 共通ヘッダ -->
    @include('parts.head')
</head>

<body>
    @include('parts.header')

    <div class="p-3 pb-2 d-flex align-items-center justify-content-center bg-info-subtle">
        <h1 class="h2">ユーザ編集</h1>
    </div>

    <div class="p-3">
        <form method="POST" action="{{ route('users.update', $user->id) }}">
            @csrf

            {{-- ★パスワードメッセージ --}}
            @if ($errors->any())
            <div style="color:red; margin-bottom:10px;">
                <ul class="error-message">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            {{-- ★ここまで --}}


            <div class="form-group">
                <label class="form-label">ID</label>
                <input type="text" class="form-inputA" value="{{ $user->id }}" readonly>
            </div>

            <div class="form-group">
                <label class="form-label">名前</label>
                <input type="text" name="name" class="form-input" value="{{ $user->name }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">メールアドレス</label>
                <input type="email" name="email" class="form-input" value="{{ $user->email }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">パスワード</label>
                <p class="form-pass">※パスワードは変更したいときだけ入力してください※</p>
                <input type="password" name="password" class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label">パスワード(確認用)</label>
                <input type="password" name="password_confirmation" class="form-input">
            </div>



            <div class="form-group">
                

                {{-- ログインユーザーが管理者なら編集可能 --}}
                @if (auth()->user()->role_id == 99)

                <label class="form-label">権限</label>
                <select name="role_id" class="role-group">
                    <option value="0" {{ $user->role_id == 0 ? 'selected' : '' }}>一般</option>
                    <option value="99" {{ $user->role_id == 99 ? 'selected' : '' }}>管理者</option>
                </select>

                {{-- ログインユーザーが一般なら表示だけ（変更不可） --}}
                @else

                <!-- <input type="text" class="role-group"
                    value="{{ $user->role_id == 99 ? '管理者' : '一般' }}" readonly> -->

                {{-- 強制的に元のroleを送る hidden --}}
                <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                @endif
            </div>
            <br>

            <div class="p-group">
                <p><button type="submit" class="btn-blue" name="user_update">更新</button></p>

            </div>
        </form>
        @if (auth()->user()->role_id == 99)
        <div class="p-group">
            <form method="POST" action="{{ route('users.delete', $user->id) }}">
                @csrf
                <p><button type="submit" class="btn-red" name="user_delete">削除</button></p>
            </form>
        </div>
        @endif
        </table>
    </div>
</body>

</html>