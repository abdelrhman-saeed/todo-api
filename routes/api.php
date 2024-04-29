<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// authentication endpoints

Route::controller(AuthController::class)
        ->group(function () {
            Route::post('login', 'login')->name('login');
            Route::get('refresh', 'refresh')->name('refresh'); // refreshing the jwt token
            Route::get('logout', 'logout')->name('logout');
        }
);

