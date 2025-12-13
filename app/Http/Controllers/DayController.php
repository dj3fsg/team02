<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DayController extends Controller
{
    public function show(string $date)
    {
        return view('day.show',[
            'date' => $date,
        ]);
    }
}
