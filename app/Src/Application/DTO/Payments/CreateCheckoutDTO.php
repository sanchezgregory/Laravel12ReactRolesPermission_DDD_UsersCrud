<?php

namespace App\Src\Application\DTO\Payments;

readonly class CreateCheckoutDTO
{
    public function __construct(
        public int $userId,
        public ?int $mediatorId,
        public ?string $email,
        public string $method,
        public int $amountMinor,
        public string $currency,
        public ?string $topic,
    ) {}

    public function getMetadata(): array
    {
        return [
            'user_id' => $this->userId,
            'mediator_id' => $this->mediatorId,
            'amount_minor' => $this->amountMinor,
        ];
    }
}
