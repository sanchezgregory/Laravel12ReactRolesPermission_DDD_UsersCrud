<?php

namespace App\Src\Domain\Contracts\ServiceContracts;

use App\Src\Domain\Entities\UserEntity;

interface UserServiceInterface
{
    public function findById(int $id): ?UserEntity;
    public function findByEmail(string $email): ?UserEntity;
    public function getUserProfileData(int $userId): array;
    public function save(UserEntity $userEntity): UserEntity;
    public function getAll(): array;
    public function update(int $userId, UserEntity $userEntity): void;
    public function delete(int $userId): void;
}