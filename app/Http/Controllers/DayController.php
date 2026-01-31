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
        $date   = Carbon::parse($date);

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

        // 予定 / タスク
        $items = Item::where('user_id', $userId)
            ->where('status_id', '<>', 99)
            // 「当日($date)に重なる」条件：sche_start <= date <= sche_end
            ->whereDate('sche_start', '<=', $date)
            ->whereDate('sche_end', '>=', $date)
            // 並び順：終日(type_id=2)を上に、それ以外は開始時刻順
            ->orderByRaw('CASE WHEN type_id = 2 THEN 0 ELSE 1 END')
            ->orderBy('sche_start', 'asc')
            ->get();

        // 表示用の時間文字列を付与（Bladeを簡単にする）
        $items->transform(function ($item) {
            // 終日
            if ((int) $item->type_id === 2) {
                $item->display_time = '00:00 〜 23:59';
                return $item;
            }

            // 時刻あり（sche_start/sche_endがある前提）
            $s = Carbon::parse($item->sche_start)->format('H:i');
            $e = Carbon::parse($item->sche_end)->format('H:i');
            $item->display_time = "{$s} 〜 {$e}";

            return $item;
        });

        // 収支（一覧用）
        $accounts = Account::where('user_id', $userId)
            ->whereDate('date', $date)
            ->where('status_id', '<>', 99)
            ->get();

        // 今日の収入合計
        $incomeTotal = Account::where('user_id', $userId)
            ->whereDate('date', $date)
            ->where('status_id', '<>', 99)
            ->where('subcategory_id', 3)
            ->sum('amount');

        // 今日の支出合計（正の合計）
        $expenseTotal = Account::where('user_id', $userId)
            ->whereDate('date', $date)
            ->where('status_id', '<>', 99)
            ->where('subcategory_id', 4)
            ->sum('amount');

        // 今日の収支（±）
        $netDay = $incomeTotal - $expenseTotal;

        // 今月（当月）の範囲
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth   = $date->copy()->endOfMonth();

        // 今月の収入合計
        $incomeTotalMonth = Account::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('status_id', '<>', 99)
            ->where('subcategory_id', 3)
            ->sum('amount');

        // 今月の支出合計
        $expenseTotalMonth = Account::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('status_id', '<>', 99)
            ->where('subcategory_id', 4)
            ->sum('amount');

        // 今月の収支（±）
        $netMonth = $incomeTotalMonth - $expenseTotalMonth;

        return view('calendar.show', [
            'date'             => $date,
            'items'            => $items,
            'accounts'         => $accounts,
            'incomeTotal'      => $incomeTotal,
            'expenseTotal'     => $expenseTotal,
            'subcategories'    => $subcategories,
            'category'         => $category,
            'netDay'           => $netDay,
            'incomeTotalMonth' => $incomeTotalMonth,
            'expenseTotalMonth'=> $expenseTotalMonth,
            'netMonth'         => $netMonth,
        ]);
    }
}