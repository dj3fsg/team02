<?php

use App\Http\Controllers\ItemController;
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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/calendar',[ItemController::class, 'index']);
Route::get('/calendar/create',[ItemController::class, 'create']);
Route::post('/calendar',[ItemController::class, 'store']);
Route::get('/calendar/edit/{id}',[ItemController::class, 'edit']);
// 更新用：url('calendar/update/{id}') に対して PUT で待ち受ける
Route::put('/calendar/update/{id}', [ItemController::class, 'update']);

// 削除用：url('calendar/delete/{id}') に対して DELETE で待ち受ける
Route::delete('/calendar/delete/{id}', [ItemController::class, 'delete']);