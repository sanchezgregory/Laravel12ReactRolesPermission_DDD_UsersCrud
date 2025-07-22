<?php

namespace App\Src\Infrastructure\Repositories\Eloquent;

use App\Src\Domain\Contracts\RepositoryContracts\RoleRepositoryInterface;
use Spatie\Permission\Models\Role;

class RoleEloquentRepository implements RoleRepositoryInterface
{
    public function getAll(): array
    {
        return Role::all()->pluck('name')->toArray();
    }

    public function findById(int $id): ?object
    {
        return Role::find($id);
    }

    public function save(object $entity): object
    {
        return $entity->save();
    }

    public function delete(int $id): void
    {
        Role::destroy($id);
    }
}
