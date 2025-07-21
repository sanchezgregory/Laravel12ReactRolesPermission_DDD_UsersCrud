<?php

namespace App\Src\Application\Services\Backoffice\CachingServices;

use Closure;
use Illuminate\Support\Facades\Cache;

final class BaseCacheService
{

    public function remember(string $key, int $ttl, Closure $callback)
    {
        return Cache::remember($key, $ttl, $callback);
    }

    public function forget(string $key): void
    {
        Cache::forget($key);
    }

    public function has(string $key): bool
    {
        return Cache::has($key);
    }

    public function get(string $key)
    {
        return Cache::get($key);
    }
}
