<?php

use Illuminate\Support\Facades\Route;

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
    return view('test');
});

// Route::get('/test', function () {
//     return view('test');
// });

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
