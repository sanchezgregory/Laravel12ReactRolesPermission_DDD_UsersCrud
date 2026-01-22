<?php

namespace App\Src\Domain\Entities;

class MediatorFinancialEntity
{
    public function __construct(
        public ?int $id,
        public int $userId,
        public ?int $customFeePercent,
        public array $providersData
    ) {}

    public static function fromModel($model): self
    {
        return new self(
            id: $model->id,
            userId: $model->user_id,
            customFeePercent: $model->custom_platform_fee_percent,
            providersData: $model->providers_data ?? []
        );
    }
}
