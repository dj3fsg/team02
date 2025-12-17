<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Account;
use Carbon\Carbon;

class DayController extends Controller
{
    public function show($date)
{
    $targetDate = Carbon::parse($date);

    // 予定 / タスク
    $items = Item::whereDate('sche_start', $targetDate)
        ->orderBy('sche_start')
        ->get();

    // 収入 / 支出
    $accounts = Account::whereDate('date', $targetDate)
        ->orderBy('date')
        ->get();

    // 合計
    $incomeTotal = $accounts->where('subcategory_id', 3)->sum('amount'); // 入金
    $expenseTotal = $accounts->where('subcategory_id', 4)->sum('amount'); // 出金

    return view('day.show', [
        'date' => $targetDate,
        'items' => $items,
        'accounts' => $accounts,
        'incomeTotal' => $incomeTotal,
        'expenseTotal' => $expenseTotal,
    ]);
}
}
