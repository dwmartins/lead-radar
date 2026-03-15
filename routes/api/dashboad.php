<?php

use App\Http\Controllers\DashboardController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function(){
    //Admin
    Route::middleware([EnsureUserIsAdmin::class])->group(function() {
        Route::get('/admin/dashboard', [DashboardController::class, 'AdminDashboard']);
    });

    Route::get('/dashboard', [DashboardController::class, 'dashboard']);
});