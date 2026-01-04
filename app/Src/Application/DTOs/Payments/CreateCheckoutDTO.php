<?php

namespace App\Src\Application\DTO\Payments;

final readonly class CreateCheckoutDTO
{
    public function __construct(
        public int $userId,
        public ?int $mediatorId,
        public string $method,     // 'stripe'
        public int $amountMinor,   // cents
        public string $currency,   // 'EUR'
        public ?string $topic = null,
        public array $metadata = [],
    ) {}
}
