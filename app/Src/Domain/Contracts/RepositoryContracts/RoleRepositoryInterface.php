<?php

namespace App\Src\Domain\Contracts\RepositoryContracts;

interface RoleRepositoryInterface
{
    public function findById(int $id): ?object;

    public function save(object $entity): object;

    public function delete(int $id): void;

    public function getAll(): array;
}
