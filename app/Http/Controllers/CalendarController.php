<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Item;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;


class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $baseDate = $request->date
            ? Carbon::parse($request->date)
            : Carbon::today();

        $startOfMonth = $baseDate->copy()->startOfMonth();
        $endOfMonth   = $baseDate->copy()->endOfMonth();

        // 月内に「重なる」予定を全部取る（重要）
        $itemsInMonth = Item::where('user_id', $userId)
            ->where('status_id', '<>', 99)
            ->whereDate('sche_start', '<=', $endOfMonth)
            ->whereDate('sche_end', '>=', $startOfMonth)
            ->get();

        // 日別件数（期間を各日に展開してカウント）
        $itemCounts = [];
        foreach ($itemsInMonth as $item) {
            $s = Carbon::parse($item->sche_start)->startOfDay();
            $e = Carbon::parse($item->sche_end)->startOfDay();

            $from = $s->copy()->max($startOfMonth);
            $to   = $e->copy()->min($endOfMonth);

            foreach (\Carbon\CarbonPeriod::create($from, $to) as $d) {
                $key = $d->toDateString();
                $itemCounts[$key] = ($itemCounts[$key] ?? 0) + 1;
            }
        }

        // 支出合計（日別）
        $expenseSums = Account::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('status_id', '<>', 99)
            ->where('subcategory_id', 4)
            ->selectRaw('date, SUM(amount) as sum')
            ->groupBy('date')
            ->pluck('sum', 'date');

        // 収入合計（日別）
        $incomeSums = Account::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('status_id', '<>', 99)
            ->where('subcategory_id', 3)
            ->selectRaw('date, SUM(amount) as sum')
            ->groupBy('date')
            ->pluck('sum', 'date');

        // 右サイド用：月内の予定（必要なカラムだけ）
        $items = $itemsInMonth->map(function ($item) {
            return (object)[
                'id' => $item->id,
                'title' => $item->title,
                'sche_start' => $item->sche_start,
                'sche_end' => $item->sche_end,
                'type_id' => $item->type_id,
                'status_id' => $item->status_id,
                'subcategory_id' => $item->subcategory_id,
            ];
        });

        // 月内の収支（右サイド用）
        $accounts = Account::select('id', 'title', 'date', 'amount', 'subcategory_id', 'account_category_id')
            ->where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('status_id', '<>', 99)
            ->get();

        $subcategories = [
            4 => '支出',
            3 => '収入',
        ];

        $incomeTotalMonth = Account::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('subcategory_id', 3)
            ->sum('amount');

        $expenseTotalMonth = Account::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('subcategory_id', 4)
            ->sum('amount');

        $netMonth = $incomeTotalMonth - $expenseTotalMonth;

        return view('calendar.index', [
            'itemCounts'  => $itemCounts,
            'incomeSums'  => $incomeSums,
            'expenseSums' => $expenseSums,
            'items'       => $items,
            'accounts'    => $accounts,
            'subcategory' => $subcategories,
            'baseDate'    => $baseDate,
            'netMonth'    => $netMonth,
        ]);
    }
}
