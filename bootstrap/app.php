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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureRole::class,
        ]);

        // When an authenticated user hits a guest-only route (e.g. back-button to /login),
        // redirect them to the correct home page for their role.
        $middleware->redirectUsersTo(function (Request $request) {
            $role = auth()->user()?->role;
            if ($role === 'pengurus_kandang') return route('pk.dashboard');
            if ($role === 'kepala_kandang')   return route('kk.dashboard');
            return route('dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
