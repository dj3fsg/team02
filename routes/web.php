<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DayController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\AccountController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes();

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


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout']);

Route::get('/users/{id}/edit', function () {
    return "user edit";
})->name('user_edit');

// Route::get('/accounts/{id}/edit', function () {
//     return view('accounts.edit');
// })->name('account_edit');
Route::get('/accounts/{id}/edit', [App\Http\Controllers\AccountController::class, 'Edit']);

Route::post('/message/update/{id}', [App\Http\Controllers\AccountController::class, 'Update'])->name('update');

Route::post('/message/delete/{id}', [App\Http\Controllers\AccountController::class, 'Delete'])->name('delete');

Route::get('/calendar/events/{date}', function () {
    return "g05";
})->name('g05');
