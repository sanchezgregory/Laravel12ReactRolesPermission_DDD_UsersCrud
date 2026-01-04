<?php

namespace App\Src\Domain\Contracts\PaymentContracts;

use App\Src\Domain\ValueObjects\PaymentMethod;

interface PaymentProviderResolverInterface
{
    public function resolve(PaymentMethod $method): PaymentProviderInterface;
}
