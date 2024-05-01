<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;


// authentication endpoints

Route::controller(AuthController::class)
        ->group(function () {
            Route::post('login', 'login')->name('login');
            Route::get('refresh', 'refresh')->name('refresh'); // refreshing the jwt token
            Route::get('logout', 'logout')->name('logout');
        }
);

Route::apiResource('tasks', TaskController::class)
        ->missing( fn () => response('resource not found', 404) );