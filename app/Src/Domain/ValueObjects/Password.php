<?php

namespace App\Src\Domain\ValueObjects;

use App\Src\Domain\ValueObjects\Concerns\StringValueObject;
use Stringable;
use InvalidArgumentException;

final readonly class Password implements Stringable
{
    use StringValueObject;

    public static function validate(string &$value): void
    {
        $value = trim($value);
        if (empty($value)) {
            throw new InvalidArgumentException('La contraseña no puede estar vacía.');
        }
        if (strlen($value) < 8) {
            throw new InvalidArgumentException('La contraseña debe tener al menos 8 caracteres.');
        }
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $value)) {
            throw new InvalidArgumentException('La contraseña debe contener al menos una letra minúscula, una letra mayúscula, un número y un carácter especial.');
        }
    }
}
