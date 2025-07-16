<?php

namespace App\Src\Infrastructure\Repositories\Eloquent;

use App\Models\User as UserModel;
use App\Src\Domain\Contracts\UserRepositoryInterface;
use App\Src\Domain\Entities\UserEntity;

class UserEloquentRepository implements UserRepositoryInterface
{
    public function __construct(private UserModel $model) {}

    /**
     * Método privado para mapear un Modelo Eloquent a una Entidad de Dominio.
     */
    private function toEntity(UserModel $userModel): UserEntity
    {
        return new UserEntity(
            id: $userModel->id,
            name: $userModel->name,
            email: $userModel->email,
            roles: $userModel->getRoleNames()->toArray() // Asumiendo que usas spatie/laravel-permission
        );
    }

    /**
     * Busca un usuario por su ID y lo devuelve como una Entidad de Dominio.
     */
    public function findById(int $id): ?UserEntity
    {
        $userModel = $this->model->find($id);

        return $userModel ? $this->toEntity($userModel) : null;
    }

    /**
     * Busca un usuario por su email y lo devuelve como una Entidad de Dominio.
     */
    public function findByEmail(string $email): ?UserEntity
    {
        $userModel = $this->model->where('email', $email)->first();

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
        // Busca si el modelo ya existe o crea uno nuevo
        $userModel = $this->model->findOrNew($userEntity->id);

        // Mapea los datos desde la entidad al modelo
        $userModel->name = $userEntity->name;
        $userModel->email = $userEntity->email;

        // Si es un usuario nuevo, asigna la contraseña
        if (!$userEntity->id && !empty($userEntity->password)) {
            $userModel->password = bcrypt($userEntity->password);
        }

        $userModel->save();

        // Devuelve la entidad actualizada (con el ID si era nuevo)
        return $this->toEntity($userModel);
    }

    public function update(UserEntity $userEntity): UserEntity
    {
        $userModel = $this->model->find($userEntity->id);

        $userModel->name = $userEntity->name;
        $userModel->email = $userEntity->email;

        $userModel->save();

        return $this->toEntity($userModel);
    }

    public function delete(UserEntity $userEntity): void
    {
        $userModel = $this->model->find($userEntity->id);
        $userModel->delete();
    }
}
