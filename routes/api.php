<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\LogController;

use App\Events\UserLogsEvent;
use App\Models\AdminUsersLogs;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/login', LoginController::class);

Route::post('/login', LoginController::class);

Route::post('/send-token', [LoginController::class, 'sendToken']);
Route::post('/verify', [LoginController::class, 'verify']);

Route::post('/send-request', [LoginController::class, 'sendRequest']);
Route::get('/broadcast/{id}', [LoginController::class, 'approveLogin']);

Route::post('/logout', function (Request $request) {

    event(new UserLogsEvent($request->user()->id, AdminUsersLogs::TYPE_USERS_LOGOUT, [
        'admin'  =>   $request->user()->name,
        'admin_id'  => $request->user()->id,
        'user_id'  =>  $request->user()->id,
        'user_name'  =>  $request->user()->name
    ]));

    $time_start = microtime(true);

    auth()->guard('web')->logout();
    $request->session()->invalidate();
    $time_end = microtime(true);
    $timeend = $time_end - $time_start;

    return response()->json([
        'success' => true,
        '_elapsed_time' => $timeend,
    ], 200);
});

Route::post('/register', [RegisterController::class, 'register']);

Route::post('/fetch/requests', [LogController::class, 'fetch']);



// Route::post('/register_admin', function (Request $request) {
//     $time_start = microtime(true);
//     $time_end = microtime(true);
//     $timeend = $time_end - $time_start;

//     return response()->json([
//         'success' => true,
//         '_elapsed_time' => $timeend,
//     ], 200);
// });


Route::group(['prefix' => 'user', 'middleware' => 'throttle:500,1'], function () {

    Route::post('/user_datatable', [UserController::class, 'user_datatable']);

    Route::post('/datatable', [UserController::class, 'datatable']);

    Route::delete('/delete/{id}', [UserController::class, 'delete']);

    Route::post('/resetpassword/{id}', [UserController::class, 'resetpassword']);

    Route::post('/changestatus/{id}', [UserController::class, 'changestatus']);

    Route::post('/changepassword/{id}', [UserController::class, 'changepassword']);

    Route::post('/update_username/{id}', [UserController::class, 'update_name']);

    Route::post('/register', [UserController::class, 'register_admin']);
});

Route::group(['prefix' => 'role', 'middleware' => 'throttle:500,1'], function () {

    Route::get('/data', [RoleController::class, 'get_roles']);

    Route::post('/update_role/{id}', [RoleController::class, 'update_role']);

    Route::post('/datatable', [RoleController::class, 'datatable']);

    Route::post('/create', [RoleController::class, 'create']);

    Route::delete('/delete/{id}', [RoleController::class, 'delete']);
});

Route::group(['prefix' => 'logs', 'middleware' => 'throttle:500,1'], function () {

    Route::post('/datatable', [LogController::class, 'datatable']);
});
