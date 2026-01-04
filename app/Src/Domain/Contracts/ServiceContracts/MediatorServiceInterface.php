<?php

namespace App\Src\Domain\Contracts\ServiceContracts;

use App\Src\Domain\Entities\MediatorEntity;

interface MediatorServiceInterface
{
    /** @return array<int, MediatorEntity> */
    public function getAll(): array;

    public function findById(int $id): ?MediatorEntity;
}
