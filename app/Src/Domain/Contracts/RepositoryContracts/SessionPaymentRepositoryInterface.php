<?php

namespace App\Src\Domain\Contracts\RepositoryContracts;

use App\Src\Domain\Entities\SessionPaymentEntity;

interface SessionPaymentRepositoryInterface
{
    public function findById(int $id): ?SessionPaymentEntity;

    public function findByProviderSessionId(string $providerSessionId): ?SessionPaymentEntity;

    public function save(SessionPaymentEntity $payment): SessionPaymentEntity;

    public function update(int $id, SessionPaymentEntity $payment): SessionPaymentEntity;

    public function checkStatusPayment(array $data): ?array;
}
