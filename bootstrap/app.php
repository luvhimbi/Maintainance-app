<?php

//use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CampusMemberMiddleware;
use App\Http\Middleware\preventBack;
use App\Http\Middleware\TechnicianMiddleware;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware ->alias(
            [
                'prevent-back'=> preventBack::class,
                'campus_member'=>CampusMemberMiddleware::class,
                'technician'=>TechnicianMiddleware::class,
                'admin'=>AdminMiddleware::class,
            ]
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
