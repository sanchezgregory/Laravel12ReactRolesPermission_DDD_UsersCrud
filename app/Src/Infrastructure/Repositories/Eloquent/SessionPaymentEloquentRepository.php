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
}
