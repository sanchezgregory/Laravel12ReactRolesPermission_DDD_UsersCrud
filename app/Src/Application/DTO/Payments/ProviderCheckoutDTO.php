<?php

namespace App\Src\Application\DTO\Payments;

readonly class ProviderCheckoutDTO
{
    public function __construct(
        public string $providerSessionId,
        public string $redirectUrl,
        public array $raw = [],

        // public string $redirectUrl,
        // public string $providerSessionId,
        // public array $raw = [],
    ) {}
}
