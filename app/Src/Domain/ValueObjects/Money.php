<?php

namespace App\Src\Domain\ValueObjects;

use App\Src\Domain\ValueObjects\Currency;
use InvalidArgumentException;
use JsonSerializable;

final readonly class Money implements JsonSerializable
{
    public function __construct(
        public int $amountMinor,
        public Currency $currency
    ) {
        if ($amountMinor <= 0) {
            throw new InvalidArgumentException('Amount must be greater than 0.');
        }
    }

    public static function from(int $amountMinor, Currency $currency): self
    {
        return new self($amountMinor, $currency);
    }

    public function jsonSerialize(): mixed
    {
        return [
            'amount_minor' => $this->amountMinor,
            'currency' => $this->currency->value,
        ];
    }
}
