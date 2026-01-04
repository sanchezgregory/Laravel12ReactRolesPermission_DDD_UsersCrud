<?php

namespace App\Src\Domain\ValueObjects;

use App\Src\Domain\ValueObjects\Concerns\StringValueObject;
use InvalidArgumentException;
use JsonSerializable;
use Stringable;

final readonly class PaymentStatus implements Stringable, JsonSerializable
{
    use StringValueObject;

    public const PENDING = 'pending';
    public const PAID = 'paid';
    public const FAILED = 'failed';
    public const EXPIRED = 'expired';

    public static function validate(string &$value): void
    {
        $value = strtolower(trim($value));

        if ($value === '') {
            throw new InvalidArgumentException('Payment status is required.');
        }

        $allowed = [self::PENDING, self::PAID, self::FAILED, self::EXPIRED];

        if (!in_array($value, $allowed, true)) {
            throw new InvalidArgumentException('Invalid payment status.');
        }
    }
}
