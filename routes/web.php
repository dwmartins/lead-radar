<?php

use Illuminate\Support\Facades\Route;

/**
 * Rotas que iniciam com /api
 */
Route::prefix('api')->group(function() {
    foreach (glob(base_path('routes/api/*.php')) as $routeFile) {
        require $routeFile;
    }
});

/**
 * Demais rotas incia o Vue
 */
Route::get('/{any}', function () {
    return view('base');
})->where('any', '^(?!api).*');