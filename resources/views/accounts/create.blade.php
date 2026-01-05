<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>収支作成</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #2c3e50;
            color: #fff;
            padding: 16px;
            text-align: center;
        }

        /* .container {
            max-width: 420px;
            margin: 40px auto;
            background-color: #fff;
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        } */

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
        }

        input,
        select {
            width: 100%;
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #3498db;
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 24px;
        }

        button {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #2980b9;
        }

        .back-link {
            text-decoration: none;
            color: #555;
            font-size: 14px;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
    @include('parts.head')
</head>

<body>
    @include('parts.header')

    <div class="p-3 pb-2 d-flex align-items-center justify-content-center bg-info-subtle">
        <h1 class="h2">収支作成</h1>
    </div>

    <div class="container p-3">

        <form method="POST" action="{{ route('accounts.store') }}">
            @csrf

            <!-- 日付 -->
            <div class="form-group">
                <label>日付</label>
                <input type="date" name="date" required>
            </div>

            <!-- 区分 -->
            <div class="form-group">
                <label>区分</label>
                <select name="subcategory_id" required>
                    <option value="0">選択してください</option>
                    <option value="3">収入</option>
                    <option value="4">支出</option>
                </select>
            </div>

            <!-- 金額 -->
            <div class="form-group">
                <label>金額</label>
                <input type="number" name="amount" required>
            </div>

            <!-- タイトル -->
            <div class="form-group">
                <label>タイトル</label>
                <input type="text" name="title">
            </div>



            <!-- カテゴリ -->
            <div class="form-group">
                <label>カテゴリ</label>
                <select name="account_category_id">
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
            <div class="form-group">
                <label>メモ</label>
                <input type="text" name="memo">
            </div>

            <!-- ボタン -->
            <div class="button-group">
                <button type="submit">登録する</button>
                <!-- <a href="#" class="back-link">戻る</a> -->
            </div>
        </form>

    </div>

</body>

</html>