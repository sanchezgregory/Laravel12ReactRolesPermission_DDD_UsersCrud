<?php

namespace App\Src\Infrastructure\Repositories\Eloquent;

use App\Models\SessionPayment as SessionPaymentModel;
use App\Src\Domain\Contracts\RepositoryContracts\SessionPaymentRepositoryInterface;
use App\Src\Domain\Entities\SessionPaymentEntity;
use App\Src\Infrastructure\Exceptions\RepositoryException;
use Illuminate\Support\Facades\Log;

class SessionPaymentEloquentRepository implements SessionPaymentRepositoryInterface
{
    public function __construct(private SessionPaymentModel $model) {}

    private function toEntity(SessionPaymentModel $model): SessionPaymentEntity
    {
        return SessionPaymentEntity::fromArray($model->toArray());
    }

    public function findById(int $id): ?SessionPaymentEntity
    {
        try {
            $m = $this->model->find($id);
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }

        return $m ? $this->toEntity($m) : null;
    }

    public function findByProviderSessionId(string $providerSessionId): ?SessionPaymentEntity
    {
        try {
            $m = $this->model->where('provider_session_id', $providerSessionId)->first();
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }

        return $m ? $this->toEntity($m) : null;
    }

    public function save(SessionPaymentEntity $payment): SessionPaymentEntity
    {
        try {
            $m = $this->model->create($payment->toArray());
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }

        return $this->toEntity($m);
    }

    public function update(int $id, SessionPaymentEntity $payment): SessionPaymentEntity
    {
        try {
            $m = $this->model->findOrFail($id);
            $m->fill($payment->toArray());
            $m->save();
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }

        return $this->toEntity($m);
    }

    public function checkStatusPayment(array $data): ?array
    {
        try {
            $m = $this->model->where('provider_session_id', $data['session_id'])->first();

            if (!$m || $m->status !== 'paid') {
                return null;
            }

            Log::info('SessionPaymentEloquentRepository', ['model' => $m->toArray()]);

            return [
                'paid' => $m->status === 'paid',
                'mediator' => $m->mediator_id,
            ];

        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getByMediatorId(int $mediatorId): array
    {
        try {
            $models = $this->model->with('user')->where('mediator_id', $mediatorId)->orderBy('created_at', 'desc')->get();
            return $models->map(function ($m) {
                $arr = $m->toArray();
                $arr['client_name'] = $m->user ? $m->user->name : null;
                $arr['email'] = $m->user ? $m->user->email : null;
                return SessionPaymentEntity::fromArray($arr);
            })->toArray();
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getClientsByMediatorId(int $mediatorId): array
    {
        try {
            $userIds = $this->model->where('mediator_id', $mediatorId)
                ->distinct()
                ->pluck('user_id');

            return \App\Models\User::whereIn('id', $userIds)->get()->toArray();
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function hasActivePayment(int $userId, int $mediatorId): bool
    {
        // Check if there is any payment with status 'paid' for this user and mediator
        // that hasn't been scheduled yet (scheduled_at is null)
        return $this->model->where('user_id', $userId)
            ->where('mediator_id', $mediatorId)
            ->where('status', 'paid')
            ->whereNull('scheduled_at') // Only sessions pending scheduling
            ->exists();
    }

    public function getActiveSessionsByUserId(int $userId): array
    {
        try {
            // Find payments with status 'paid' for this user
            // EXCLUDING those that are scheduled in the past
            $models = $this->model->with('mediator')
                ->where('user_id', $userId)
                ->where('status', 'paid')
                ->where(function ($query) {
                    $query->whereNull('scheduled_at')
                          ->orWhere('scheduled_at', '>', now());
                })
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->transformToSessionArray($models);

        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getAllSessionsByUserId(int $userId): array
    {
        try {
            $models = $this->model->with('mediator')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->transformToSessionArray($models);

        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function transformToSessionArray($models): array
    {
        return $models->map(function ($m) {
            $arr = $m->toArray();
            $arr['mediator_name'] = $m->mediator ? $m->mediator->name : 'Unknown';
            $arr['mediator_email'] = $m->mediator ? $m->mediator->email : 'Unknown';
            return $arr;
        })->toArray();
    }
}
