<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Item;
use App\Models\Account;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        // 基準日（指定がなければ今日）
        $baseDate = $request->date
            ? Carbon::parse($request->date)
            : Carbon::today();

        $startOfMonth = $baseDate->copy()->startOfMonth();
        $endOfMonth   = $baseDate->copy()->endOfMonth();

        // 予定件数（日別）
        $itemCounts = Item::whereBetween('sche_start', [$startOfMonth, $endOfMonth])
            ->selectRaw('DATE(sche_start) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');

        // 支出合計（日別） subcategory_id = 2
        $expenseSums = Account::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('type_id', 1)
            ->selectRaw('date, SUM(amount) as sum')
            ->groupBy('date')
            ->pluck('sum', 'date');

        // 収入合計（日別） subcategory_id = 1
        $incomeSums = Account::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('type_id', 2)
            ->selectRaw('date, SUM(amount) as sum')
            ->groupBy('date')
            ->pluck('sum', 'date');

        // 月内の予定（右サイド用）
        $items = Item::select('id', 'title', 'sche_start', 'status_id', 'subcategory_id')
            ->whereBetween('sche_start', [$startOfMonth, $endOfMonth])
            ->get();

        // 月内の収支（右サイド用）
        $accounts = Account::select('id', 'title', 'date', 'amount', 'subcategory_id', 'type_id')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get();

        $kubun = [
            1 => '支出',
            2 => '収入',
        ];

        return view('calendar.index', [
            'itemCounts'  => $itemCounts,
            'incomeSums'  => $incomeSums,
            'expenseSums' => $expenseSums,
            'items'       => $items,
            'accounts'    => $accounts,
            'kubun'       => $kubun,
        ]);
    }
}
