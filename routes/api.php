<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;

Route::get('test', function (Request $request) {
    return 'success';
})->middleware('jwt.verify');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::apiResource('/posts', App\Http\Controllers\Api\PostController::class);