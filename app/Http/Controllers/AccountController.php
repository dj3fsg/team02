<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;

class AccountController extends Controller
{
    //
    public function Edit($id)
    {
        $account = Account::find($id);
        return view('accounts.edit', compact('account'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'date' => 'required|date',
        ]);

        $account = Account::findOrFail($id);

        $priceInput = $request->input('amount', $account->amount); // '10,000'
        $price = (float)str_replace(',', '', $priceInput); // カンマとスペースを除去して数値に変換

        $account->date = $request->input('date', $account->date);
        $account->subcategory_id = $request->input('subcategory_id', $account->subcategory_id);
        $account->amount = $price;
        $account->title = $request->input('title', $account->title);
        $account->memo = $request->input('memo', $account->memo);
        $account->update();

        return redirect()->route('g05', ['date' => $account->date->format('Y-m-d')]);
    }

    public function delete(Request $request, $id)
    {
        $account = Account::findOrFail($id);
        //$this->authorize('delete', $account);
        //$account->delete();

        //論理削除で対応する
        $account->status_id = 99;
        $account->update();

        return redirect()->route('g05', ['date' => $account->date->format('Y-m-d')]);
    }
}
