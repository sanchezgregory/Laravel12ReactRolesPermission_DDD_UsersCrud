<?php

namespace App\Src\Domain\Contracts\PaymentContracts;

use App\Src\Application\DTO\Payments\CreateCheckoutDTO;
use App\Src\Application\DTO\Payments\ProviderCheckoutDTO;
use App\Src\Application\DTO\Payments\WebhookResultDTO;
use App\Src\Domain\ValueObjects\PaymentMethod;

interface PaymentProviderInterface
{
    public function method(): PaymentMethod;

    public function createCheckout(CreateCheckoutDTO $dto, int $paymentId): ProviderCheckoutDTO;

    public function handleWebhook(string $payload, array $headers): WebhookResultDTO;
}
