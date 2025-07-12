<?php

namespace App\Src\Domain\ValueObjects;

abstract class BaseValueObject
{
    abstract public function equals(self $other): bool;
    
    abstract public function __toString(): string;
}