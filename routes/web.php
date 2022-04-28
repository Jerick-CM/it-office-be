<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use App\Http\Controllers\UserController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\Admin\LogController as LogConAdmin;


Route::get('/', function () {
    return view('welcome');
});

Route::get('users/export/', [UserController::class, 'export']);
Route::get('login-request/export/', [LogController::class, 'export']);
Route::get('user-logs/export/', [LogConAdmin::class, 'userlogs_export']);
