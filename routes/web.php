<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'admin'], function(){
    Route::view('login', 'login')->name('admin.login');
    Route::view('/', 'dashboard')->name('admin.dashboard');

});