<?php

use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DayController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;

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

Route::middleware('auth')->group(function () {

    // カレンダー（G04）
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');

    // 当日詳細（G05）
    Route::get('/calendar/events/{date}', [DayController::class, 'show'])->name('calendar.events.show');

    // 予定（Item）
    Route::get('/calendar/create', [ItemController::class, 'create']);

    Route::post('/calendar', [ItemController::class, 'store']);

    Route::get('/calendar/edit/{id}', [ItemController::class, 'edit'])->name('items.edit');

    Route::put('/calendar/update/{id}', [ItemController::class, 'update']);

    Route::delete('/calendar/delete/{id}', [ItemController::class, 'delete']);


    // 収支（Account）
    Route::get('/accounts/create', [AccountController::class, 'create'])->name('accounts.create');

    Route::post('/accounts', [AccountController::class, 'store'])->name('accounts.store');

    Route::get('/account/edit/{id}', [AccountController::class, 'edit'])->name('accounts.edit');

    Route::put('/account/update/{id}', [AccountController::class, 'update'])->name('accounts.update');

    Route::delete('/account/delete/{id}', [AccountController::class, 'delete'])->name('accounts.delete');

    // ユーザー管理
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    
    Route::post('/users/{id}/update', [UserController::class, 'update'])->name('users.update');
    
    Route::post('/users/{id}/delete', [UserController::class, 'destroy'])->name('users.delete');

    // ログアウト
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

    Route::post('/message/update/{id}', [App\Http\Controllers\AccountController::class, 'Update'])->name('update');

    Route::post('/message/delete/{id}', [App\Http\Controllers\AccountController::class, 'Delete'])->name('delete');
});


// Route::get('/accounts/{id}/edit', function () {
//     return view('accounts.edit');
// })->name('account_edit');







Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
