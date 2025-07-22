<?php

namespace App\Src\Domain\ValueObjects\Concerns;

trait StringValueObject
{
    public readonly string $value;

    /**
     * El constructor es privado para forzar la creación a través del factory method.
     */
    private function __construct(string $value)
    {
        // Llama al método de validación que debe ser implementado por la clase que usa el trait.
        static::validate($value);
        $this->value = $value;
    }

    /**
     * Factory method para crear la instancia.
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }

    /**
     * Declara un método abstracto que cada clase debe implementar.
     * Aquí es donde irá la lógica de validación específica.
     */
    abstract public static function validate(string &$value): void;
}
