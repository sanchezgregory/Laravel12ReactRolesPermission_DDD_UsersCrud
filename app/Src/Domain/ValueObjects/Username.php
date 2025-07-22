<?php

namespace App\Src\Domain\ValueObjects;

use App\Src\Domain\ValueObjects\Concerns\StringValueObject;
use Stringable;
use InvalidArgumentException;
use JsonSerializable;

final readonly class Username implements Stringable, JsonSerializable
{
    use StringValueObject;

    public static function validate(string &$value): void
    {
        $value = strtolower(trim($value));

        if (strlen($value) < 3) {
            throw new InvalidArgumentException('El nombre de usuario debe tener al menos 3 caracteres.');
        }
        if (!preg_match('/^[a-z0-9_]+$/', $value)) {
            throw new InvalidArgumentException('El nombre de usuario solo puede contener letras minúsculas, números y guiones bajos.');
        }
    }
}
