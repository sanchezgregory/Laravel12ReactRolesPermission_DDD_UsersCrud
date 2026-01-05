<?php

namespace App\Src\Application\DTO\Payments;

readonly class WebhookResultDTO
{
    public function __construct(
        public bool $handled,
        public string $eventType,
        public ?string $providerSessionId,
        public ?string $status, // paid|failed|expired|null
        public ?string $paymentIntentId = null,
        public array $meta = [],
    ) {}
}
