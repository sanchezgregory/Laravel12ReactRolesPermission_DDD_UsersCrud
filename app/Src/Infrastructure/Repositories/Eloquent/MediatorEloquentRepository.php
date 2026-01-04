<?php

namespace App\Src\Infrastructure\Repositories\Eloquent;

use App\Models\User;
use App\Src\Domain\Contracts\RepositoryContracts\MediatorRepositoryInterface;
use App\Src\Domain\Entities\MediatorEntity;
use App\Src\Infrastructure\Exceptions\RepositoryException;

class MediatorEloquentRepository implements MediatorRepositoryInterface
{
    public function __construct(private User $model) {}

    private function toEntity(User $user): MediatorEntity
    {
        $profile = $user->mediatorProfile;

        return MediatorEntity::fromArray([
            'id' => $user->id,
            'name' => (string) $user->name,
            'email' => (string) $user->email,
            'session_price_minor' => (int) ($profile?->session_price_minor ?? 0),
            'currency' => (string) ($profile?->currency ?? 'EUR'),
            'calendly_url' => $profile?->calendly_url,
            'headline' => $profile?->headline,
            'bio' => $profile?->bio,
        ]);
    }

    public function getAll(): array
    {
        try {
            return $this->model
                ->role('mediator') // Spatie: filtra usuarios con role mediator
                ->with('mediatorProfile')
                ->orderBy('name')
                ->get()
                ->map(fn (User $u) => $this->toEntity($u))
                ->toArray();
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function findById(int $id): ?MediatorEntity
    {
        try {
            $user = $this->model
                ->role('mediator')
                ->with('mediatorProfile')
                ->whereKey($id)
                ->first();

            return $user ? $this->toEntity($user) : null;
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
