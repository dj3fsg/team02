<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    // 収支作成画面
    public function create()
    {
        return view('accounts.create');
    }

    // 収支登録処理
    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'date'       => 'required|date',
            'subcategory_id' => 'required|integer',
            'amount'     => 'required|numeric|min:0',
            'memo'       => 'nullable|string|max:255',
        ]);

        // 登録
        Account::create([
            // 'user_id'        => Auth::id(),
            'user_id'        => 1,
            'subcategory_id'=> $request->subcategory_id,
            'type_id'        => $request->type, 
            'status_id'      => 1, // 仮：有効
            'date'           => $request->date,
            'title'          => $request->title,
            'amount'         => $request->amount,
            'memo'           => $request->memo,
        ]);

        // G05（当日詳細）へ戻る
        return redirect()->route('calendar.events.show', ['date' => $request->date]);
    }
}
