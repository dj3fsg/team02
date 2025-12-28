<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Account;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class DayController extends Controller
{
    public function show($date)
    {
        $userId = Auth::id();

        $subcategories = [
            3 => '収入',
            4 => '支出',
        ];

        $category = [
            1 => '食費',
            2 => '日用品',
            3 => '交通費',
            4 => '家賃',
            5 => '娯楽',
            6 => '給料',
            9 => 'その他',
        ];
        $date = Carbon::parse($date);

        // 予定 / タスク
        $items = Item::where('user_id', $userId)
            ->whereDate('sche_start', $date)
            ->where('status_id', '<>', 99)
            ->get();


        // 収入 / 支出（一覧用）
        $accounts = Account::where('user_id', $userId)
            ->whereDate('date', $date)
            ->where('status_id', '<>', 99)
            ->get();

        // todayカード用 集計
        $incomeTotal = Account::where('user_id', $userId)
            ->whereDate('date', $date)
            ->where('status_id', '<>', 99)
            ->where('subcategory_id', 3)
            ->sum('amount');


        $expenseTotal = Account::where('user_id', $userId)
            ->whereDate('date', $date)
            ->where('status_id', '<>', 99)
            ->where('subcategory_id', 4)
            ->sum('amount');

        return view('calendar.show', compact(
            'date',
            'items',
            'accounts',
            'incomeTotal',
            'expenseTotal',
            'subcategories',
            'category'

        ));
    }
}
