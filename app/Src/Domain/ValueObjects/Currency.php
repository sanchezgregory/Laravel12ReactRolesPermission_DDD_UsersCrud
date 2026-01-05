<?php

namespace App\Src\Domain\ValueObjects;

use App\Src\Domain\ValueObjects\Concerns\StringValueObject;
use InvalidArgumentException;
use JsonSerializable;
use Stringable;

final readonly class Currency implements Stringable, JsonSerializable
{
    use StringValueObject;

    public static function validate(string &$value): void
    {
        $value = strtoupper(trim($value));

        if (!preg_match('/^[A-Z]{3}$/', $value)) {
            throw new InvalidArgumentException('Currency must be a 3-letter ISO code.');
        }
    }

    public function jsonSerialize(): mixed
    {
        return $this->value;
    }
}
