<?php

namespace App\Providers;

use App\Src\Application\Services\Backoffice\CachingServices\BaseCacheService;
use App\Src\Application\Services\UserService;
use App\Src\Domain\Contracts\RepositoryContracts\MediatorRepositoryInterface;
use App\Src\Domain\Contracts\RepositoryContracts\RoleRepositoryInterface;
use App\Src\Domain\Contracts\RepositoryContracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Vite;
use Illuminate\Contracts\Debug\ExceptionHandler;
use App\Src\Infrastructure\Handlers\CustomExceptionHandler;
use App\Src\Infrastructure\Repositories\Eloquent\MediatorEloquentRepository;
use App\Src\Infrastructure\Repositories\Eloquent\UserEloquentRepository;
use App\Src\Infrastructure\Repositories\Eloquent\RoleEloquentRepository;
use App\Src\Domain\Contracts\RepositoryContracts\SessionPaymentRepositoryInterface;
use App\Src\Domain\Contracts\ServiceContracts\UserServiceInterface;
use App\Src\Infrastructure\Repositories\Eloquent\SessionPaymentEloquentRepository;

class AppServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(BaseCacheService::class, fn() => new BaseCacheService());

        $this->app->extend(ExceptionHandler::class, function ($handler, $app) {
            return new CustomExceptionHandler($handler);
        });

        // Decorator and Service
        // $this->decorate(UserServiceInterface::class, UserService::class, UserCachingService::class);

        // Repositories
        $this->app->bind(UserRepositoryInterface::class, UserEloquentRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleEloquentRepository::class);
        $this->app->bind(SessionPaymentRepositoryInterface::class, SessionPaymentEloquentRepository::class);
        $this->app->bind(MediatorRepositoryInterface::class, MediatorEloquentRepository::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
