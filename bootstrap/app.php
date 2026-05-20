<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // ─── Spatie Permission Middleware Aliases ─────────────────────────
        $middleware->alias([
            'role'               => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        // ─── CORS: izinkan React frontend ─────────────────────────────────
        // HandleCors sudah otomatis aktif di Laravel 12 via config/cors.php
    })
    ->withProviders([
        // ─── Repository Binding ───────────────────────────────────────────
        App\Providers\RepositoryServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions): void {
        // ─── Handle Unauthenticated ───────────────────────────────────────
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return \App\Helpers\ApiResponse::unauthorized('Silakan login terlebih dahulu.');
            }
        });

        // ─── Handle Not Found ─────────────────────────────────────────────
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return \App\Helpers\ApiResponse::notFound('Data tidak ditemukan.');
            }
        });

        // ─── Handle Forbidden (403 — role tidak cukup) ────────────────────
        $exceptions->render(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return \App\Helpers\ApiResponse::forbidden('Akses ditolak. Anda tidak memiliki izin.');
            }
        });
    })->create();
