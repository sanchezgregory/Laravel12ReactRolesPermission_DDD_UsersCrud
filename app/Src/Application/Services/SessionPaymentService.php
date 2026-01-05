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
}