<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, Request $request) {
            if (app()->hasDebugModeEnabled() === false) {
                $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

                if($request->is('api/*') && $status === 500) {
                    return response()->json([
                        'message' => 'Ops, ocorreu um erro, tente novamente.',
                    ], 500);
                }

                if ($status === 500) {
                    return response()->view('errors.500', [], 500);
                }
            }
        });
    })->create();
