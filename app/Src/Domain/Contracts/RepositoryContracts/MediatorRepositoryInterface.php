<?php

namespace App\Src\Domain\Contracts\RepositoryContracts;

use App\Src\Domain\Entities\MediatorEntity;

interface MediatorRepositoryInterface
{
    /** @return array<int, MediatorEntity> */
    public function getAll(): array;

    public function findById(int $id): ?MediatorEntity;
}
