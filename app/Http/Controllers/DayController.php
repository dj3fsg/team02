<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Account;

class DayController extends Controller
{
    public function show(string $date)
    {
       $items = Item::whereDate('sche_start', $date)
            ->orderBy('sche_start')
            ->get();

       $accounts = Account::whereDate('date', $date)
            ->orderBy('date')
            ->get();

        $incomeTotal = $accounts
            ->where('amount', '>' , 0)
            ->sum('amount');

        $expenseTotal = $accounts
        ->where('amount', '<', 0)
        ->sum('amount');

       return view('day.show', [
        'date' => $date,
        'items' => $items,
        'accounts' => $accounts,
        'incomeTotal' => $incomeTotal,
        'expenseTotal' => $expenseTotal,
       ]);
    }
}
