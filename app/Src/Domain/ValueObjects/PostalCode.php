<?php

namespace App\Src\Domain\ValueObjects;

use App\Src\Domain\ValueObjects\Concerns\StringValueObject;
use Stringable;
use InvalidArgumentException;
use JsonSerializable;

final readonly class PostalCode implements Stringable, JsonSerializable
{
    use StringValueObject;

    public static function validate(string &$value): void
    {
        $value = trim($value);
        if (!preg_match('/^[A-Z0-9]{4,10}$/', $value)) {
            throw new InvalidArgumentException("El código postal '{$value}' no es válido.");
        }
    }
}
