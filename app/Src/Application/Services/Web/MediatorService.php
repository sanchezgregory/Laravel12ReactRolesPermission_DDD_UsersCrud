<?php

namespace App\Src\Application\Services\Web;

use App\Src\Domain\Contracts\RepositoryContracts\MediatorRepositoryInterface;
use App\Src\Domain\Contracts\ServiceContracts\MediatorServiceInterface;
use App\Src\Domain\Entities\MediatorEntity;

class MediatorService implements MediatorServiceInterface
{
    public function __construct(private readonly MediatorRepositoryInterface $repository) {}

    /** @return array<int, MediatorEntity> */
    public function getAll(): array
    {
        return $this->repository->getAll();
    }

    public function findById(int $id): ?MediatorEntity
    {
        return $this->repository->findById($id);
    }
}
