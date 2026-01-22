<?php

namespace App\Src\Infrastructure\Repositories;

use App\Models\GlobalSetting;
use App\Models\MediatorFinancial;
use App\Src\Domain\Contracts\RepositoryContracts\PaymentSettingsRepositoryInterface;
use App\Src\Domain\Entities\MediatorFinancialEntity;

class PaymentSettingsEloquentRepository implements PaymentSettingsRepositoryInterface
{
    private const GLOBAL_FEE_KEY = 'global_platform_fee_percent';
    private const DEFAULT_FEE = 30;

    public function getGlobalFeePercent(): int
    {
        $setting = GlobalSetting::where('key', self::GLOBAL_FEE_KEY)->first();
        return $setting ? (int) $setting->value : self::DEFAULT_FEE;
    }

    public function setGlobalFeePercent(int $percent): void
    {
        GlobalSetting::updateOrCreate(
            ['key' => self::GLOBAL_FEE_KEY],
            ['value' => (string) $percent]
        );
    }

    public function getMediatorFinancial(int $mediatorId): ?MediatorFinancialEntity
    {
        $model = MediatorFinancial::where('user_id', $mediatorId)->first();
        if (!$model) {
            return null;
        }
        return MediatorFinancialEntity::fromModel($model);
    }

    public function saveMediatorFinancial(int $mediatorId, ?int $feePercent, ?array $providersData): void
    {
        MediatorFinancial::updateOrCreate(
            ['user_id' => $mediatorId],
            [
                'custom_platform_fee_percent' => $feePercent,
                'providers_data' => $providersData
            ]
        );
    }
}
