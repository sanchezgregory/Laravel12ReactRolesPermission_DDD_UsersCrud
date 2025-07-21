<?php

namespace App\Listeners;

use App\Src\Application\Services\Backoffice\CachingServices\BaseCacheService;
use Illuminate\Contracts\Events\Dispatcher;
use App\Events\UserUpdated;
use App\Events\UserDeleted;
use App\Events\UserCreated;
use App\Src\Application\Services\Backoffice\CachingServices\CacheKeys\AppCacheKeys;

class UserCacheSubscriber
{
    public function __construct(private BaseCacheService $cacheService) {}

    /**
     * Maneja el evento cuando un usuario es actualizado.
     */
    public function handleUserUpdate(UserUpdated $event): void
    {
        $cacheKey = AppCacheKeys::USER_DATA->key($event->userId);
        $this->cacheService->forget($cacheKey);
    }

    /**
     * Maneja el evento cuando un usuario es eliminado.
     */
    public function handleUserDelete(UserDeleted $event): void
    {
        $cacheKey = AppCacheKeys::USER_DATA->key($event->userId);
        $this->cacheService->forget($cacheKey);
    }

    public function handleUserCreate(UserCreated $event): void
    {
        $this->cacheService->forget(AppCacheKeys::USERS_LIST->key(null));
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(UserUpdated::class, [self::class, 'handleUserUpdate']);
        $events->listen(UserDeleted::class, [self::class, 'handleUserDelete']);
        $events->listen(UserCreated::class, [self::class, 'handleUserCreate']);
    }
}
