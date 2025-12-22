<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DayController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\AccountController;

// 初期画面（不要なら消してOK）
Route::get('/', function () {
    return view('welcome');
});

Route::get('/calendar', [CalendarController::class, 'index'])
    ->name('calendar.index');

Route::get('/calendar/events/{date}', [DayController::class, 'show'])
    ->name('calendar.events.show');

// 収支作成画面（表示）
Route::get('/accounts/create', [AccountController::class, 'create'])
    ->name('accounts.create');

// 収支登録（POST）
Route::post('/accounts', [AccountController::class, 'store'])
    ->name('accounts.store');
