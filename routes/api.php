<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

// Route::get('test', function (Request $request) {
//     return 'success';
// })->middleware('jwt.verify');

Route::post('webhook', [WebhookController::class, 'getStatus']);

Route::post('register', [AuthController::class, 'register']);

Route::post('login', [AuthController::class, 'login']);

Route::post('is_email_exist', [UserController::class, 'isEmailExist']);

Route::group(['middleware' => 'jwt.verify'], function($router){
    Route::get('users', [UserController::class, 'show']);

    Route::get('users/{username}', [UserController::class, 'getUserByUsername']);

    Route::put('update', [UserController::class, 'update']);

    Route::post('logout', [AuthController::class, 'logout']);
});

// Route::apiResource('/posts', App\Http\Controllers\Api\PostController::class);