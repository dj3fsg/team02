<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Account_category;
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
            'account_category_id' => 'required|integer',
            'amount'     => 'required|numeric|min:0',
            'memo'       => 'nullable|string|max:255',
        ]);

        // 登録
        Account::create([
            'user_id'        => Auth::id(),
            //'user_id'        => 1,
            'account_category_id' => $request->account_category_id,
            'subcategory_id' => $request->subcategory_id,
            'type_id'        => null,
            'status_id'      => 1, // 仮：有効
            'date'           => $request->date,
            'title'          => $request->title,
            'amount'         => $request->amount,
            'memo'           => $request->memo,
        ]);

        // G05（当日詳細）へ戻る
        return redirect()->route('calendar.events.show', ['date' => $request->date]);
    }

    //
    public function edit($id)
    {
        $account = Account::find($id);
        $account_categories = Account_category::all();
        return view('accounts.edit', compact('account', 'account_categories'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'date' => 'required|date',
        ]);

        $account = Account::findOrFail($id);

        $priceInput = $request->input('amount', $account->amount); // '10,000'
        //$price = (float)str_replace(',', '', $priceInput); // カンマとスペースを除去して数値に変換
        $price = filter_var(str_replace(',', '', $priceInput), FILTER_VALIDATE_FLOAT);
        if ($price === false) {
            $errors = [
                'error' => "amount must be number", //"Cannot cast '{$priceInput}' to float. Invalid numeric format."
            ];
            return  redirect()->back()->withErrors($errors);
        }
        if ($price < 0) {
            // abort(404, 'amount cannot be negative.');
            $errors = [
                'error' => "amount cannot be negative.",
            ];
            return  redirect()->back()->withErrors($errors);
        }

        $account_category_id = $request->input('account_category_id', $account->account_category_id);
        if ($account_category_id == 0) {
            $errors = [
                'error' => "account category must be selected.",
            ];
            return  redirect()->back()->withErrors($errors);
        }

        $account->date = $request->input('date', $account->date);
        $account->subcategory_id = $request->input('subcategory_id', $account->subcategory_id);
        $account->amount = $price;
        $account->title = $request->input('title', $account->title);
        $account->account_category_id = $account_category_id;
        $account->memo = $request->input('memo', $account->memo);
        $account->update();

        return redirect()->route('calendar.events.show', [
            'date' => \Carbon\Carbon::parse($account->date)->format('Y-m-d')
        ])->with('success', '更新しました');
    }

    public function delete(Request $request, $id)
    {
        $account = Account::findOrFail($id);
        //$this->authorize('delete', $account);
        //$account->delete();

        //論理削除で対応する
        $account->status_id = 99;
        $account->update();

        return redirect()->route('calendar.events.show', [
            'date' => \Carbon\Carbon::parse($account->date)->format('Y-m-d')
        ])->with('success', '削除しました');
    }
}
