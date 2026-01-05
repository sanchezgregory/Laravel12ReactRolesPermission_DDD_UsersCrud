<?php

namespace App\Src\Application\Services;

use App\Src\Domain\Contracts\RepositoryContracts\UserRepositoryInterface;
use App\Src\Domain\Entities\UserEntity;

class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function getUserProfileData(int $userId): array
    {
        return $this->userRepository->getUserProfileData($userId);
    }

    public function update(int $userId, array $userEntity): void
    {
        $userEntity = UserEntity::fromArray($userEntity);
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

    public function save(array $user): void
    {
        $userEntity = UserEntity::fromArray($user);
        $this->userRepository->save($userEntity);
    }

    public function delete(int $userId): void
    {
        $this->userRepository->delete($userId);
    }
}
