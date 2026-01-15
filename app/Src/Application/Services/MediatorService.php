<?php

namespace App\Src\Application\Services;

use App\Src\Domain\Contracts\RepositoryContracts\MediatorRepositoryInterface;
use App\Src\Domain\Entities\MediatorEntity;

class MediatorService
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

    public function save(array $data): void
    {
        $entity = MediatorEntity::fromArray($data);
        $this->repository->save($entity);
    }

    public function update(int $id, array $data): void
    {
        $entity = MediatorEntity::fromArray($data);
        $this->repository->update($id, $entity);
    }

    public function delete(int $id): void
    {
        $this->repository->delete($id);
    }
}
