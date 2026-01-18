<?php

namespace App\Src\Application\Services;

use App\Src\Domain\Contracts\RepositoryContracts\SessionPaymentRepositoryInterface;
use App\Src\Domain\Entities\SessionPaymentEntity;

class SessionPaymentService
{
    public function __construct(
        private readonly SessionPaymentRepositoryInterface $repo
    ) {}

    public function findById(int $id): ?SessionPaymentEntity
    {
        return $this->repo->findById($id);
    }

    public function checkStatusPayment(array $data): ?array
    {
        return $this->repo->checkStatusPayment($data);
    }

    public function getByMediatorId(int $mediatorId): array
    {
        return $this->repo->getByMediatorId($mediatorId);
    }

    public function getClientsByMediatorId(int $mediatorId): array
    {
        return $this->repo->getClientsByMediatorId($mediatorId);
    }

    public function hasActivePayment(int $userId, int $mediatorId): bool
    {
        return $this->repo->hasActivePayment($userId, $mediatorId);
    }

    public function getActiveSessionsByUserId(int $userId): array
    {
        return $this->repo->getActiveSessionsByUserId($userId);
    }

    public function getAllSessionsByUserId(int $userId): array
    {
        return $this->repo->getAllSessionsByUserId($userId);
    }
}