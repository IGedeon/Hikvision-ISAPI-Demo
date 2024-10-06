<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/store', 'App\Http\Controllers\HikvisionController@store');