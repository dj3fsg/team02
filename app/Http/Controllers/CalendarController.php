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
        // 基準日（指定がなければ今日）
        $userId = Auth::id();

        $baseDate = $request->date
            ? Carbon::parse($request->date)
            : Carbon::today();

        $startOfMonth = $baseDate->copy()->startOfMonth();
        $endOfMonth   = $baseDate->copy()->endOfMonth();

        // 予定件数（日別）
        $itemCounts = Item::where('user_id', $userId)
            ->whereBetween('sche_start', [$startOfMonth, $endOfMonth])
            ->where('status_id', '<>', 99)
            ->selectRaw('DATE(sche_start) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');

        // 支出合計（日別） subcategory_id = 4
        $expenseSums = Account::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('status_id', '<>', 99)
            ->where('subcategory_id', 4)
            ->selectRaw('date, SUM(amount) as sum')
            ->groupBy('date')
            ->pluck('sum', 'date');

        // 収入合計（日別） subcategory_id = 3
        $incomeSums = Account::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('status_id', '<>', 99)
            ->where('subcategory_id', 3)
            ->selectRaw('date, SUM(amount) as sum')
            ->groupBy('date')
            ->pluck('sum', 'date');

        // 月内の予定（右サイド用）
        $items = Item::select('id', 'title', 'sche_start', 'status_id', 'subcategory_id')
            ->where('user_id', $userId)
            ->whereBetween('sche_start', [$startOfMonth, $endOfMonth])
            ->where('status_id', '<>', 99)
            ->get();

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

        return view('calendar.index', [
            'itemCounts'  => $itemCounts,
            'incomeSums'  => $incomeSums,
            'expenseSums' => $expenseSums,
            'items'       => $items,
            'accounts'    => $accounts,
            'subcategory' => $subcategories,
        ]);
    }
}
