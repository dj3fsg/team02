<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Account_category;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
        $request->validate(
            [
                'date'                => 'required|date',
                'account_category_id'  => 'required|integer|exists:account_categories,id',
                'subcategory_id'       => 'required|integer|exists:subcategories,id',
                'title'               => 'nullable|string|max:255',
                'amount'              => 'required|numeric|min:0',
                'memo'                => 'nullable|string|max:255',
            ],
            [
                // ===== メッセージ =====
                'date.required'               => '日付は必須です。',
                'date.date'                   => '日付の形式が正しくありません。',

                'account_category_id.required' => 'カテゴリを選択してください。',
                'account_category_id.integer'  => 'カテゴリの指定が不正です。',
                'account_category_id.exists'   => '選択されたカテゴリは存在しません。',

                'subcategory_id.required'     => 'サブカテゴリを選択してください。',
                'subcategory_id.integer'      => 'サブカテゴリの指定が不正です。',
                'subcategory_id.exists'       => '選択されたサブカテゴリは存在しません。',

                'title.max'                   => 'タイトルは255文字以内で入力してください。',

                'amount.required'             => '金額を入力してください。',
                'amount.numeric'              => '金額は数値で入力してください。',
                'amount.min'                  => '金額は0以上で入力してください。',

                'memo.max'                    => 'メモは255文字以内で入力してください。',
            ],
            [
                // ===== 属性名 =====
                'date'               => '日付',
                'account_category_id' => 'カテゴリ',
                'subcategory_id'      => 'サブカテゴリ',
                'title'              => 'タイトル',
                'amount'             => '金額',
                'memo'               => 'メモ',
            ]
        );

        // 登録
        Account::create([
            'user_id'             => Auth::id(),
            'account_category_id' => $request->account_category_id,
            'subcategory_id'      => $request->subcategory_id,
            'type_id'             => null,
            'status_id'           => 1, // 仮：有効
            'date'                => $request->date,
            'title'               => $request->title,
            'amount'              => $request->amount,
            'memo'                => $request->memo,
        ]);

        // G05（当日詳細）へ戻る
        return redirect()->route('calendar.events.show', ['date' => $request->date]);
    }

    public function edit($id)
    {
        $account = Account::find($id);
        $account_categories = Account_category::all();

        return view('accounts.edit', compact('account', 'account_categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(
            [
                'date'                => 'required|date',
                'account_category_id'  => 'required|integer|exists:account_categories,id',
                'subcategory_id'       => 'required|integer|exists:subcategories,id',
                'title'               => 'nullable|string|max:255',
                'amount'              => 'required|numeric|min:0',
                'memo'                => 'nullable|string|max:255',
            ],
            [
                // ===== メッセージ =====
                'date.required'               => '日付は必須です。',
                'date.date'                   => '日付の形式が正しくありません。',

                'account_category_id.required' => 'カテゴリを選択してください。',
                'account_category_id.integer'  => 'カテゴリの指定が不正です。',
                'account_category_id.exists'   => '選択されたカテゴリは存在しません。',

                'subcategory_id.required'     => 'サブカテゴリを選択してください。',
                'subcategory_id.integer'      => 'サブカテゴリの指定が不正です。',
                'subcategory_id.exists'       => '選択されたサブカテゴリは存在しません。',

                'title.max'                   => 'タイトルは255文字以内で入力してください。',

                'amount.required'             => '金額を入力してください。',
                'amount.numeric'              => '金額は数値で入力してください。',
                'amount.min'                  => '金額は0以上で入力してください。',

                'memo.max'                    => 'メモは255文字以内で入力してください。',
            ],
            [
                // ===== 属性名 =====
                'date'               => '日付',
                'account_category_id' => 'カテゴリ',
                'subcategory_id'      => 'サブカテゴリ',
                'title'              => 'タイトル',
                'amount'             => '金額',
                'memo'               => 'メモ',
            ]
        );

        $account = Account::findOrFail($id);

        $priceInput = $request->input('amount', $account->amount); // '10,000'
        $price = filter_var(str_replace(',', '', $priceInput), FILTER_VALIDATE_FLOAT);

        if ($price === false) {
            return redirect()->back()->withErrors([
                'error' => 'amount must be number',
            ]);
        }

        if ($price < 0) {
            return redirect()->back()->withErrors([
                'error' => 'amount cannot be negative.',
            ]);
        }

        $account_category_id = $request->input('account_category_id', $account->account_category_id);

        if ($account_category_id == 0) {
            return redirect()->back()->withErrors([
                'error' => 'account category must be selected.',
            ]);
        }

        $account->date                = $request->input('date', $account->date);
        $account->subcategory_id       = $request->input('subcategory_id', $account->subcategory_id);
        $account->amount              = $price;
        $account->title               = $request->input('title', $account->title);
        $account->account_category_id = $account_category_id;
        $account->memo                = $request->input('memo', $account->memo);
        $account->update();

        return redirect()->route('calendar.events.show', [
            'date' => Carbon::parse($account->date)->format('Y-m-d'),
        ])->with('success', '更新しました');
    }

    public function delete(Request $request, $id)
    {
        $account = Account::findOrFail($id);

        // 論理削除で対応する
        $account->status_id = 99;
        $account->update();

        return redirect()->route('calendar.events.show', [
            'date' => Carbon::parse($account->date)->format('Y-m-d'),
        ])->with('success', '削除しました');
    }
}
