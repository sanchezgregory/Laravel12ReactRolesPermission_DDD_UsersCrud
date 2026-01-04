<?php

namespace App\Src\Application\DTO\Payments;

final readonly class ProviderCheckoutDTO
{
    public function __construct(
        public string $redirectUrl,
        public string $providerSessionId,
        public array $raw = [],
    ) {}
}
