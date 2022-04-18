
<?php

use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'post'], function () {
});

Route::group(['prefix' => 'users'], function () {

    Route::get('/', [UsersController::class, 'show']);
    Route::get('/{email}', [UsersController::class, 'show']);
    Route::get('/username/{username}', [UsersController::class, 'show_username']);
    Route::post('/create', [UsersController::class, 'create']);
    Route::post('/datatable', [UsersController::class, 'datatable']);

    Route::post('/delete', [UsersController::class, 'destroy']);

    Route::post('/update/', [UsersController::class, 'update']);
    Route::get('/{slug}', [UsersController::class, 'index']);

    Route::post('/datatable_logs', [UsersController::class, 'datatable_logs']);
});
