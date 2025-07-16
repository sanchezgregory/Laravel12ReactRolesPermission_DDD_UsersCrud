<?php

namespace App\Src\Application\Services;

use App\Src\Domain\Contracts\UserRepositoryInterface;
use App\Src\Domain\Entities\UserEntity;

class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function findById(int $id): ?UserEntity
    {
        return $this->userRepository->findById($id);
    }

    public function findByEmail(string $email): ?UserEntity
    {
        return $this->userRepository->findByEmail($email);
    }

    public function getAll(): array
    {
        return $this->userRepository->getAll();
    }

    public function save(UserEntity $user): UserEntity
    {
        return $this->userRepository->save($user);
    }

    public function update(UserEntity $user): UserEntity
    {
        return $this->userRepository->update($user);
    }

    public function delete(UserEntity $user): void
    {
        $this->userRepository->delete($user);
    }
}
