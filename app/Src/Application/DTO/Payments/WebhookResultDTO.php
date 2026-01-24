<?php

namespace App\Src\Application\DTO\Payments;

use App\Src\Domain\ValueObjects\PaymentStatus;

readonly class WebhookResultDTO
{
    public function __construct(
        public bool $handled,
        public string $eventType,
        public ?string $providerSessionId,
        public ?PaymentStatus $status, // PaymentStatus object or null
        public ?string $paymentIntentId = null,
        public array $meta = [],
    ) {}
}
