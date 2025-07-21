<?php

namespace App\Src\Application\Services\Backoffice;

use App\Src\Domain\Contracts\RepositoryContracts\UserRepositoryInterface;
use App\Src\Domain\Contracts\ServiceContracts\UserServiceInterface;
use App\Src\Domain\Entities\UserEntity;

class UserService implements UserServiceInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function getUserProfileData(int $userId): array
    {
        return $this->userRepository->getUserProfileData($userId);
    }

    public function update(int $userId, UserEntity $userEntity): void
    {
        $this->userRepository->update($userId, $userEntity);
    }

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

    public function delete(int $userId): void
    {
        $this->userRepository->delete($userId);
    }
}
