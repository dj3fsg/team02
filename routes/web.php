<?php

use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DayController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\UserController;

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

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/calendar'); // ログイン済みならメイン画面
    } else {
        return view('auth.login'); // 未ログインならログイン画面
    }
});

Route::get('/calendar/create', [ItemController::class, 'create']);
Route::post('/calendar', [ItemController::class, 'store']);
Route::get('/calendar/edit/{id}', [ItemController::class, 'edit']);
// 更新用：url('calendar/update/{id}') に対して PUT で待ち受ける
Route::put('/calendar/update/{id}', [ItemController::class, 'update']);

// 削除用：url('calendar/delete/{id}') に対して DELETE で待ち受ける
Route::delete('/calendar/delete/{id}', [ItemController::class, 'delete']);

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

Route::get('/users', [UserController::class, 'index'])->name('users.index');
// 編集画面
Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
// 更新処理
Route::post('/users/{id}/update', [UserController::class, 'update'])->name('users.update');
// 削除
Route::post('/users/{id}/delete', [UserController::class, 'destroy'])->name('users.delete');


// Route::get('/accounts/{id}/edit', function () {
//     return view('accounts.edit');
// })->name('account_edit');
Route::get('/accounts/{id}/edit', [App\Http\Controllers\AccountController::class, 'Edit']);

Route::post('/message/update/{id}', [App\Http\Controllers\AccountController::class, 'Update'])->name('update');

Route::post('/message/delete/{id}', [App\Http\Controllers\AccountController::class, 'Delete'])->name('delete');

Route::get('/calendar/events/{date}', function () {
    return "g05";
})->name('g05');
