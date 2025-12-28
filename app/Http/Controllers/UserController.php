<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; //パスワードチェック
use Illuminate\Support\Facades\Auth;


//チーム開発
class UserController extends Controller
{

    // ①ユーザ一覧
    public function index()
    {          

        $role = [
            0 => '一般',
            99 => '管理者'
        ];

        $users = User::all();

        return view('users.index', compact('users', 'role'));
    }

    // ②編集画面
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $loginUser = auth()->user(); // ←ログイン中のユーザを取得

        return view('users.edit', compact('user', 'loginUser'));
    }

   // ③更新画面
public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    // ★ バリデーション（ここで止まる）
    $request->validate(
        [
        'name' => 'required',
        'email' => 'required|email',
        'password' => 'nullable|confirmed',
        ],
    [
        'password.confirmed' => 'パスワードが一致していません。',
        ]);

    // 名前、、メール
    $user->name  = $request->name;
    $user->email = $request->email;

    // ★ パスワードは入力された時だけ
    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }


 // ★ 権限処理（最重要）
        if (Auth::user()->role_id == 0) {
            // 一般ユーザ → 変更禁止（強制0）
            $user->role_id = 0;
        } else {
            // 管理者 → 変更可能
            $user->role_id = $request->role;
        }

    $user->save();

    return redirect()->route('users.index')->with('success', '更新しました');
}

    // ④削除処理
    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->route('users.index')->with('success', '削除しました');
    }
}