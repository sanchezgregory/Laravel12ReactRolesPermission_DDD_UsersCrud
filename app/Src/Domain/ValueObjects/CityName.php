<?php

namespace App\Src\Domain\ValueObjects;

use App\Src\Domain\ValueObjects\Concerns\StringValueObject;
use Stringable;
use InvalidArgumentException;
use JsonSerializable;

final readonly class CityName implements Stringable, JsonSerializable
{
    use StringValueObject;

    public static function validate(string &$value): void
    {
        $value = trim($value);
        if (empty($value)) {
            throw new InvalidArgumentException('El nombre de la ciudad no puede estar vacío.');
        }
    }
}
