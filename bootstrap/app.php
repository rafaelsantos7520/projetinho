<?php

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withCommands()
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->web(prepend: \App\Http\Middleware\InitializeTenancy::class);
        $middleware->api(append: \App\Http\Middleware\InitializeTenancy::class);

        $middleware->prependToPriorityList(Authenticate::class, \App\Http\Middleware\RequireTenant::class);
        $middleware->prependToPriorityList(RedirectIfAuthenticated::class, \App\Http\Middleware\RequireTenant::class);

        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            $tenant = $request->query('tenant');
            if ($tenant) {
                return route('tenant_admin.login', ['tenant' => $tenant]);
            }
            if ($request->is('platform/*')) {
                return route('platform.login');
            }

            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
