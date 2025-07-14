<?php 

namespace App\Src\Domain\Contracts;

use App\Src\Domain\Entities\User as UserEntity;

interface UserRepositoryInterface extends BaseEntityRepository
{
    public function findById(int $id): ?UserEntity;

    public function findByEmail(string $email): ?UserEntity;

    public function save(UserEntity $user): UserEntity;

    public function all(): array;
}