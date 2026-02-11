<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Account;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Subcategory;
use App\Models\Account_category;
class DayController extends Controller
{
    public function show($date)
    {
        $userId = Auth::id();
        $date   = Carbon::parse($date);

        // 表示用に、区分・カテゴリをマスタDBから id => 名称 の配列で取得
        $subcategories = Subcategory::pluck('subcategory', 'id')->toArray();
        $category      = Account_category::pluck('name', 'id')->toArray();

        // 予定 / タスク
        $items = Item::where('user_id', $userId)
                ->where('status_id', '<>', 99)
                ->where(function ($q) use ($date) {
                    $q->where(function ($q2) use ($date) {
                        // 予定：当日に重なる（開始<=当日<=終了）
                        $q2->whereDate('sche_start', '<=', $date)
                            ->whereDate('sche_end',   '>=', $date);
                    })
                    // タスク：当日が期限
                    ->orWhereDate('sche_done', $date);
                })
                ->orderByRaw('CASE WHEN type_id = 2 THEN 0 ELSE 1 END')
                ->orderBy('sche_start', 'asc')
                ->get();


        // 表示用の時間文字列を付与（Bladeを簡単にする）
        $items->transform(function ($item) {
            // 終日
            

            if($item->subcategory_id == 1){
                if ((int) $item->type_id === 2) {
                    $item->display_time = '終日';
                }else{
                // 時刻あり（sche_start/sche_end）
                    $s = Carbon::parse($item->sche_start)->format('H:i');
                    $e = Carbon::parse($item->sche_end)->format('H:i');
                    $item->display_time = "{$s} 〜 {$e}";
                }ぎ
               
            }else{
                $d = Carbon::parse($item->sche_done)->format('H:i');
                $item->display_time = " 〜 {$d}";

            }

            

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