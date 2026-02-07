<?php

use App\Http\Middleware\AdminAuthMiddleware;
use App\Http\Middleware\AuthCheckMiddleware;
use App\Http\Middleware\ForceJsonResponse;
use App\Http\Middleware\PreventBackHistory;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->group('api', [
            ForceJsonResponse::class,
        ]);

        // alias register
        $middleware->alias([
            'prevent-back-history' => PreventBackHistory::class,
            'admin_auth' => AdminAuthMiddleware::class,
            'auth_check' => AuthCheckMiddleware::class,
        ]);

    })
    ->withExceptions(function ($exceptions) {
        // API Route Not Found
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'API route not found.',
                    'path' => $request->path(),
                ], 404);
            }
        });

        // Wrong HTTP Method
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid request method.',
                ], 405);
            }
        });
    })
    ->create();

