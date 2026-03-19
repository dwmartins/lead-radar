<?php

use App\Http\Controllers\SubscriptionController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', EnsureUserIsAdmin::class])->group(function() {
    Route::get('/subscription', [SubscriptionController::class, 'index']);
});