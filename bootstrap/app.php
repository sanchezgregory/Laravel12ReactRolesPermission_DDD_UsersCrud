<?php

use App\Src\Infrastructure\Exceptions\ExceptionTransformer;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Src\Infrastructure\Middleware\HandleAppearance;
use App\Src\Infrastructure\Middleware\HandleInertiaRequests;
use App\Src\Infrastructure\Services\ExceptionService;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Excepciones para las rutas API (no validan csrf tokens)
        $middleware->validateCsrfTokens(except: [
            'api/*',
            'payments/stripe/webhook',
            'payments/stripe/success',
            'payments/stripe/cancel',
            'webhooks/payments/mercadopago',
        ]);

        $middleware->redirectGuestsTo(fn () => route('login'));

        $middleware->redirectUsersTo(function () {
            $user = \Illuminate\Support\Facades\Auth::user();
            if ($user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'mediator'])) {
                return route('backoffice.dashboard');
            }
            return route('user.sessions');
        });

        $middleware->trustProxies(at: '*');

        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->alias([
            'auth.backoffice' => \App\Src\Infrastructure\Middleware\EnsureUserIsAuthenticated::class,
            'auth.api' => \App\Src\Infrastructure\Middleware\EnsureApiIsAuthenticated::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        $middleware->web(append: [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            HandleInertiaRequests::class,
            HandleAppearance::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (\Throwable $e, \Illuminate\Http\Request $request) {
            $transformedException = ExceptionTransformer::transform($e);
            return app(ExceptionService::class)->handle($transformedException, $request);
        });
    })
    ->create();
