<?php

namespace App\Providers;

use App\Src\Domain\Contracts\UserRepositoryInterface;
use App\Src\Infrastructure\Repositories\Eloquent\UserEloquentRepository;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Debug\ExceptionHandler;
use App\Src\Infrastructure\Handlers\CustomExceptionHandler;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->extend(ExceptionHandler::class, function ($handler, $app) {
            return new CustomExceptionHandler($handler);
        });

        $this->app->bind(UserRepositoryInterface::class, UserEloquentRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
