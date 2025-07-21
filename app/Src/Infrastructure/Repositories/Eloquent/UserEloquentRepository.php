<?php

namespace App\Src\Infrastructure\Repositories\Eloquent;

use App\Models\User as UserModel;
use App\Src\Domain\Contracts\RepositoryContracts\UserRepositoryInterface;
use App\Src\Domain\Entities\UserEntity;
use App\Src\Infrastructure\Exceptions\RepositoryException;

class UserEloquentRepository implements UserRepositoryInterface
{
    public function __construct(private UserModel $model) {}

    /**
     * Método privado para mapear un Modelo Eloquent a una Entidad de Dominio.
     */
    private function toEntity(UserModel $userModel): UserEntity
    {
        
        return UserEntity::fromArray($userModel->toArray());
    }

    /**
     * Busca un usuario por su ID y lo devuelve como una Entidad de Dominio.
     */
    public function findById(int $id): ?UserEntity
    {
        try {
            $userModel = $this->model->find($id);
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }

        return $userModel ? $this->toEntity($userModel) : null;
    }

    /**
     * Busca un usuario por su email y lo devuelve como una Entidad de Dominio.
     */
    public function findByEmail(string $email): ?UserEntity
    {
        try {
            $userModel = $this->model->where('email', $email)->first();
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }

        return $userModel ? $this->toEntity($userModel) : null;
    }

    /**
     * Devuelve todos los usuarios como un array de Entidades de Dominio.
     */
    public function getAll(): array
    {
        return $this->model->all()->map(fn($userModel) => $this->toEntity($userModel))->toArray();
    }

    /**
     * Persiste una Entidad de Dominio en la base de datos.
     */
    public function save(UserEntity $userEntity): UserEntity
    {
        try {
            // Busca si el modelo ya existe o crea uno nuevo
            $userModel = $this->model->findOrNew($userEntity->id);

            // Mapea los datos desde la entidad al modelo
            $userModel->name = $userEntity->name;
            $userModel->email = $userEntity->email;

            // Si es un usuario nuevo, asigna la contraseña
            if (!$userEntity->id && !empty($userEntity->password)) {
                $userModel->password = bcrypt($userEntity->password);
            }

            // Guarda el modelo
            $userModel->save();
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }
        return $this->toEntity($userModel);
    }

    public function update(int $userId, UserEntity $userEntity): void
    {
        try {
            $userModel = $this->model->find($userId);
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }
        $userModel->name = $userEntity->name;
        $userModel->email = $userEntity->email;
        if (!empty($userEntity->password)) {
            $userModel->password = bcrypt($userEntity->password);
        }       
        $userModel->save();
    }

    public function delete(int $id): void
    {
        try {
            $userModel = $this->model->find($id);
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }
        $userModel->delete();
    }

    public function getUserProfileData(int $userId): array
    {
        return $this->model->find($userId)->toArray();
    }
}
