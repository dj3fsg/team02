<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Account;
use Carbon\Carbon;

class DayController extends Controller
{
    public function show($date)
    {
        $kubun = [
            2 => '収入',
            1 => '支出',
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
        $items = Item::whereDate('sche_start', $date)->get();

        // 収入 / 支出（一覧用）
        $accounts = Account::whereDate('date', $date)->get();

        // todayカード用 集計
        $incomeTotal = Account::whereDate('date', $date)
            ->where('type_id', 2) // 収入
            ->sum('amount');

        $expenseTotal = Account::whereDate('date', $date)
            ->where('type_id', 1) // 支出
            ->sum('amount');

        return view('calendar.show', compact(
            'date',
            'items',
            'accounts',
            'incomeTotal',
            'expenseTotal',
            'kubun',
            'category'

        ));
    }
}
