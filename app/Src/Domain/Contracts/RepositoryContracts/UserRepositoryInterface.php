<?php

namespace App\Src\Domain\Contracts\RepositoryContracts;

use App\Src\Domain\Entities\UserEntity;

interface UserRepositoryInterface
{
    public function findById(int $id): ?UserEntity;

    public function findByEmail(string $email): ?UserEntity;

    public function save(UserEntity $user): void;

    public function update(int $userId, UserEntity $userEntity): void;

    public function delete(int $userId): void;

    public function getAll(): array;

    public function getUserProfileData(int $userId): array;

    public function getCalendlyUrl(int $userId): ?string;
}
