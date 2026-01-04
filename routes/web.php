<?php

use App\Src\Infrastructure\Controllers\Backoffice\LogErrorController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

// API routes (require authentication)
Route::prefix('api/v1')->name('api.v1')->group(function () {
    // Public routes for API authentication
    require __DIR__ . '/api_auth.php';

    // Protected routes (require authentication and jwt)
    Route::middleware('auth.api')->group(function () {
        require __DIR__ . '/api_routes.php';
    });
});

// Auth WEB routes (must be outside auth middleware)
Route::prefix('backoffice')->name('backoffice.')->group(function () {
    // Public routes for backoffice authentication
    require __DIR__ . '/backoffice_auth.php';

    // Protected routes (require authentication and admin role)
    Route::middleware('auth.backoffice', 'role:admin')->group(function () {
        require __DIR__ . '/backoffice_routes.php';
    });
});

// Mediators routes | public
require __DIR__ . '/auth_pages.php';
require __DIR__ . '/mediators.php';

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/log-error', LogErrorController::class)->name('api.log.error');
    require __DIR__ . '/payments_routes.php';
});


Route::get('/clear-all-cache', function () {
    if (!app()->environment('production')) {
        try {
            $output = [];

            // Limpiar todas las claves de caché
            if (config('cache.default') === 'redis') {
                // Para Redis, usamos el comando flushAll
                $redis = Cache::getRedis();
                $redis->flushAll();
                $output['cache_keys'] = 'All Redis cache keys have been removed';
            } else {
                // Para otros drivers, intentamos obtener todas las claves y eliminarlas
                try {
                    $keys = Cache::getStore()->getPrefix() . '*';
                    if (method_exists(Cache::getStore(), 'getRedis')) {
                        $redis = Cache::getStore()->getRedis();
                        $keys = $redis->keys($keys);
                        if (!empty($keys)) {
                            $redis->del($keys);
                        }
                        $output['cache_keys'] = count($keys) . ' cache keys have been removed';
                    } else {
                        // Si no podemos obtener las claves, simplemente limpiamos todo el caché
                        Cache::flush();
                        $output['cache_keys'] = 'Cache has been flushed';
                    }
                } catch (\Exception $e) {
                    // Si hay algún error, intentamos con flush
                    Cache::flush();
                    $output['cache_keys'] = 'Cache has been flushed (fallback method)';
                }
            }

            // Limpiar otros caches de Laravel
            $commands = [
                'route:clear',
                'route:cache',
                'config:clear',
                'config:cache',
                'cache:clear',
                'view:clear',
                'optimize:clear',
                'event:clear',
            ];

            foreach ($commands as $command) {
                $output[$command] = Artisan::call($command);
            }

            return [
                'status' => 'success',
                'message' => 'All cache and cache keys have been cleared',
                'details' => $output
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];
        }
    }
    return abort(404);
});

// Rutas de Frontoffice (solo para usuarios)
// Route::middleware('auth', 'role:user')->group(function () {
//     require __DIR__ . '/user_routes.php';
// });
