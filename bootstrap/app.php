<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Application;
use \App\Http\Middleware\CorsMiddleware;
use \App\Http\Middleware\AdminMiddleware;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias(['sanctum' => EnsureFrontendRequestsAreStateful::class]);
        $middleware->alias(
            [
                'admin' => AdminMiddleware::class,
                'guestOrVerified' => \App\Http\Middleware\GuestOrVerified::class,
                ]
        );

        
       // $middleware->alias(['cors' => CorsMiddleware::class]);
       // $middleware->alias(['session' => StartSession::class]);
        
     })
  
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
