<?php

use App\Http\Controllers\PlanController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function() {
    Route::get('/plan', [PlanController::class, 'index']);
});