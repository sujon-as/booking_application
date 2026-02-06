<?php

use App\Http\Middleware\AdminAuthMiddleware;
use App\Http\Middleware\AuthCheckMiddleware;
use App\Http\Middleware\PreventBackHistory;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // alias register
        $middleware->alias([
            'prevent-back-history' => PreventBackHistory::class,
            'admin_auth' => AdminAuthMiddleware::class,
            'auth_check' => AuthCheckMiddleware::class,
        ]);

    })
    ->withExceptions(function ($exceptions) {
        //
    })
    ->create();

