<?php

namespace App\Src\Domain\ValueObjects;

use App\Src\Domain\ValueObjects\Concerns\StringValueObject;
use Stringable;
use InvalidArgumentException;
use JsonSerializable;

final readonly class Email implements Stringable, JsonSerializable
{
    use StringValueObject;

    public static function validate(string &$value): void
    {
        $value = trim($value);
        if (empty($value)) {
            throw new InvalidArgumentException('El correo electrónico no puede estar vacío.');
        }
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('El correo electrónico no es válido.');
        }
    }
}
