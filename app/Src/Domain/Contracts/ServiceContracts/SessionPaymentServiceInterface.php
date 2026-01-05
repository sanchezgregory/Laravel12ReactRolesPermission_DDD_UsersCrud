<?php

namespace App\Src\Domain\Contracts\ServiceContracts;

use App\Src\Application\DTO\Payments\CreateCheckoutDTO;
use App\Src\Application\DTO\Payments\CreateCheckoutResultDTO;

interface SessionPaymentServiceInterface
{
    public function createCheckout(CreateCheckoutDTO $dto): CreateCheckoutResultDTO;

    public function handleWebhook(string $method, string $payload, array $headers): void;
}
