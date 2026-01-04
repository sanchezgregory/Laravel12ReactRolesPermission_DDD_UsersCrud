<?php

namespace App\Src\Domain\ValueObjects;

use App\Src\Domain\ValueObjects\Concerns\StringValueObject;
use InvalidArgumentException;
use JsonSerializable;
use Stringable;

final readonly class PaymentMethod implements Stringable, JsonSerializable
{
    use StringValueObject;

    public const STRIPE = 'stripe';
    public const MERCADOPAGO = 'mercadopago';
    public const PAYPAL = 'paypal';
    public const GOOGLEPAY = 'googlepay';

    public static function validate(string &$value): void
    {
        $value = strtolower(trim($value));

        if ($value === '') {
            throw new InvalidArgumentException('Payment method is required.');
        }

        // Permitimos cualquier string “válido” para no limitar el futuro,
        // pero al menos obligamos a que sea alfanumérico simple.
        if (!preg_match('/^[a-z0-9_]+$/', $value)) {
            throw new InvalidArgumentException('Invalid payment method format.');
        }
    }
}
