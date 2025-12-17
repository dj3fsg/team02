<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Item;
use App\Models\Account;


class CalendarController extends Controller
{
    public function index()
    {
        $today = Carbon::today();//今月
        $year = $today->year;
        $month = $today->month;

        $startOfMonth = $today->copy()->startOfMonth();//月初
        $endOfMonth = $today->copy()->endOfMonth();//月初

        $dates = [];
        $current = $startOfMonth->copy();

        while ($current <= $endOfMonth) {
            $dates[] = $current->copy();
            $current->addDay();
        }

        // 予定件数（日別）
        $itemCounts = Item::whereBetween(
                'sche_start',
                [$startOfMonth, $endOfMonth]
            )
            ->selectRaw('DATE(sche_start) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');

        // 支出合計（日別）
        $expenseSums = Account::whereBetween(
                'date',
                [$startOfMonth, $endOfMonth]
            )
            ->where('subcategory_id', 4) // 出金
            ->selectRaw('date, SUM(amount) as sum')
            ->groupBy('date')
            ->pluck('sum', 'date');

        return view('calendar.index', [
            'year' => $year,
            'month' => $month,
            'dates' => $dates,
            'itemCounts' => $itemCounts,
            'expenseSums' => $expenseSums,
]);


    }
}
