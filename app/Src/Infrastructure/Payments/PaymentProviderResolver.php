<?php

namespace App\Src\Infrastructure\Payments;

use App\Src\Domain\Contracts\PaymentContracts\PaymentProviderInterface;
use App\Src\Domain\Contracts\PaymentContracts\PaymentProviderResolverInterface;
use App\Src\Domain\ValueObjects\PaymentMethod;
use InvalidArgumentException;

class PaymentProviderResolver implements PaymentProviderResolverInterface
{
    /** @var array<string, PaymentProviderInterface> */
    private array $providersByMethod = [];

    /**
     * @param iterable<PaymentProviderInterface> $providers
     */
    public function __construct(iterable $providers)
    {
        foreach ($providers as $provider) {
            $this->providersByMethod[(string) $provider->method()] = $provider;
        }
    }

    public function resolve(PaymentMethod $method): PaymentProviderInterface
    {
        $key = (string) $method;

        if (!isset($this->providersByMethod[$key])) {
            throw new InvalidArgumentException("Unsupported payment method: {$key}");
        }

        return $this->providersByMethod[$key];
    }
}
