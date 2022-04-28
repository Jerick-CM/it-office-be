
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LogController;

Route::group(['prefix' => 'logs'], function () {

    Route::post('/user/data-table', [LogController::class, 'userlogs_datatable']);

});
