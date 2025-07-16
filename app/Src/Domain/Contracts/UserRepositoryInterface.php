<?php

namespace App\Src\Domain\Contracts;

use App\Src\Domain\Entities\UserEntity;

interface UserRepositoryInterface
{
    public function findById(int $id): ?UserEntity;

    public function findByEmail(string $email): ?UserEntity;

    public function save(UserEntity $user): UserEntity;

    public function update(UserEntity $user): UserEntity;

    public function delete(UserEntity $user): void;

    public function getAll(): array;
}
