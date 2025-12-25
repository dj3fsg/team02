<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザ一覧</title>

    <style>
        h2 {
            text-align: center;
        }

        table {
            width: 55%;
            border-collapse: collapse;
            margin-top: 10px;
            background: white;
            margin: 0 auto;
        }

        table th,
        table td {
            border: 1px solid #555;
            padding: 8px;
            text-align: left;
        }

        table th {
            background: #eaeff7;
        }
    </style>

    @include('parts.head')
</head>

<body>
    @include('parts.header')

    <div class="p-3 pb-2 d-flex align-items-center justify-content-center bg-info-subtle">
        <h1 class="h2">ユーザ一覧画面</h1>
    </div>

    <div class="p-3">
        <table border="1">
            <tr>
                <th>ID</th>
                <th>名前</th>
                <th>メールアドレス</th>
                <th>権限</th>
                <th>詳細</th>
            </tr>

            @foreach($users as $user)

            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    {{ $role[$user->role_id]  }}
                </td>

                <!-- <br><br> -->

                <td>
                    <a href="{{ route('users.edit', $user->id) }}">>>編集</a>
                    </tb>
            </tr>
            @endforeach
        </table>
    </div>

</body>

</html>