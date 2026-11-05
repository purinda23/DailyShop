<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // 1. Pengalihan otomatis untuk tamu (guests) dan user biasa
        $middleware->redirectTo(
            guests: '/login',
            users: '/'
        );

        // 2. Daftarkan alias middleware
        $middleware->alias([
            'admin'        => \App\Http\Middleware\AdminMiddleware::class,
            'prevent-back' => \App\Http\Middleware\PreventBackHistory::class,
        ]);

        // 3. TAMBAHAN: Kecualikan route Xendit dari proteksi CSRF
        $middleware->validateCsrfTokens(except: [
            'xendit/callback', 
        ]);
        
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();