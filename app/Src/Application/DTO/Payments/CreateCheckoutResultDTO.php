<?php

namespace App\Src\Application\DTO\Payments;

readonly class CreateCheckoutResultDTO
{
    public function __construct(
        public int $paymentId,
        public string $redirectUrl
    ) {}
}
