<?php

namespace App\Src\Domain\Contracts\RepositoryContracts;

use App\Src\Domain\Entities\MediatorEntity;

interface MediatorRepositoryInterface
{
    /** @return array<int, MediatorEntity> */
    public function getAll(): array;

    public function findById(int $id): ?MediatorEntity;
    public function save(MediatorEntity $mediator): void;
    public function update(int $id, MediatorEntity $mediator): void;
    public function delete(int $id): void;
}
