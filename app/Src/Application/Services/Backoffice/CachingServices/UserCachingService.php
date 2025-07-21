<?php

namespace App\Src\Application\Services\Backoffice\CachingServices;

use App\Events\UserCreated;
use App\Events\UserDeleted;
use App\Events\UserUpdated;
use App\Src\Application\Services\Backoffice\CachingServices\AppCacheKeys;
use App\Src\Domain\Contracts\ServiceContracts\UserServiceInterface;
use App\Src\Domain\Entities\UserEntity;

class UserCachingService implements UserServiceInterface
{
    private const CACHE_TTL = 3600;

    public function __construct(
        private UserServiceInterface $decoratedService,
        private BaseCacheService $cacheService
    ) {}

    public function findById(int $id): ?UserEntity
    {
        $cacheKey = AppCacheKeys::USER_DATA->key($id);

        return $this->cacheService->remember($cacheKey, self::CACHE_TTL, function () use ($id) {
            return $this->decoratedService->findById($id);
        });
    }

    public function findByEmail(string $email): ?UserEntity
    {
        $cacheKey = AppCacheKeys::USER_DATA->key($email);

        return $this->cacheService->remember($cacheKey, self::CACHE_TTL, function () use ($email) {
            return $this->decoratedService->findByEmail($email);
        });
    }

    public function save(UserEntity $userEntity): UserEntity
    {
        $this->decoratedService->save($userEntity);
        UserCreated::dispatch($userEntity->id);
        return $userEntity;
    }

    public function update(int $userId, UserEntity $userEntity): void
    {
        $this->decoratedService->update($userId, $userEntity);
        UserUpdated::dispatch($userId);
    }

    public function delete(int $userId): void
    {
        $this->decoratedService->delete($userId);
        UserDeleted::dispatch($userId);
    }

    public function getAll(): array
    {
        $cacheKey = AppCacheKeys::USERS_LIST->key(null);

        return $this->cacheService->remember($cacheKey, self::CACHE_TTL, function () {
            return $this->decoratedService->getAll();
        });
    }

    public function getUserProfileData(int $userId): array
    {
        $cacheKey = AppCacheKeys::USER_DATA->key($userId);

        return $this->cacheService->remember($cacheKey, self::CACHE_TTL, function () use ($userId) {
            return $this->decoratedService->getUserProfileData($userId);
        });
    }
}
