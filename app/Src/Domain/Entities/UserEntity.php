<?php

namespace App\Src\Domain\Entities;

class UserEntity extends BaseEntity
{
    public function __construct(
        public ?int $id,
        public string $name,
        public ?string $email,
        public ?string $password,
        public array $roles = []
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            $data['id'] ?? null,
            $data['name'],
            $data['email'] ?? null,
            $data['password'] ?? null,
            $data['roles'] ?? []
        );
    }

    public static function fromRequest(array $request): static
    {
        return new static(
            $request['id'] ?? null,
            $request['name'],
            $request['email'] ?? null,
            $request['password'] ?? null,
            $request['roles'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'roles' => $this->roles,
        ];
    }
}
