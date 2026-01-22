<?php

namespace App\Src\Domain\Contracts\RepositoryContracts;

use App\Src\Domain\Entities\MediatorFinancialEntity;

interface PaymentSettingsRepositoryInterface
{
    public function getGlobalFeePercent(): int;
    public function setGlobalFeePercent(int $percent): void;

    public function getMediatorFinancial(int $mediatorId): ?MediatorFinancialEntity;
    public function saveMediatorFinancial(int $mediatorId, ?int $feePercent, ?array $providersData): void;
}
