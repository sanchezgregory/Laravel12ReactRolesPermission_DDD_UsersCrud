<?php

namespace App\Src\Domain\Entities;

use App\Src\Domain\ValueObjects\Email;
use App\Src\Domain\ValueObjects\Password;
use App\Src\Domain\ValueObjects\PersonName;

class UserEntity extends BaseEntity
{
    public function __construct(
        public ?int $id,
        public PersonName $name,
        public ?Email $email,
        public ?Password $password,
        public array $roles = []
    ) {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public static function fromArray(array $data): static
    {
        // Validar que los datos sean correctos
        $name = PersonName::fromString($data['name']);
        $email = Email::fromString($data['email']);
        $password = isset($data['password']) ? Password::fromString($data['password']) : null;
        $roles = $data['roles'] ?? [];

        // Crear la instancia
        return new static(
            $data['id'] ?? null,
            $name,
            $email,
            $password,
            $roles
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'roles' => $this->roles,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
