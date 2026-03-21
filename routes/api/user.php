<?php

use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', EnsureUserIsAdmin::class])->group(function() {
    Route::get('/user', [UserController::class, 'index']);
    Route::post('/user', [UserController::class, 'store']);
    Route::put('/user', [UserController::class, 'update']);
    Route::get('/user/{id}', [UserController::class, 'getById'])->where('id', '[0-9]+');
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->where('id', '[0-9]+');
    Route::get('/user/get-all', [UserController::class, 'getAll']);
});