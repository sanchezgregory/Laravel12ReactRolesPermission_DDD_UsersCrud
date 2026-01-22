<?php

namespace App\Src\Application\Services;

use App\Src\Domain\Contracts\RepositoryContracts\PaymentSettingsRepositoryInterface;
use App\Src\Domain\Entities\MediatorFinancialEntity;

class PaymentConfigurationService
{
    public function __construct(
        private readonly PaymentSettingsRepositoryInterface $repository
    ) {}

    public function getGlobalPlatformFeePercent(): int
    {
        return $this->repository->getGlobalFeePercent();
    }

    public function updateGlobalPlatformFeePercent(int $percent): void
    {
        $this->repository->setGlobalFeePercent($percent);
    }

    public function getMediatorFinancial(int $mediatorId): ?MediatorFinancialEntity
    {
        return $this->repository->getMediatorFinancial($mediatorId);
    }

    public function saveMediatorFinancial(int $mediatorId, ?int $feePercent, ?array $providersData): void
    {
        // Validation could be added here
        $this->repository->saveMediatorFinancial($mediatorId, $feePercent, $providersData);
    }

    /**
     * Returns the platform fee percentage for a specific mediator.
     * Logic: Use custom if defined, otherwise use global.
     */
    public function getEffectivePlatformFeePercent(int $mediatorId): int
    {
        $financial = $this->getMediatorFinancial($mediatorId);
        if ($financial && $financial->customFeePercent !== null) {
            return $financial->customFeePercent;
        }

        return $this->getGlobalPlatformFeePercent();
    }

    /**
     * Returns the account ID for a specific provider (e.g., 'stripe') if set.
     */
    public function getProviderAccountId(int $mediatorId, string $provider): ?string
    {
        $financial = $this->getMediatorFinancial($mediatorId);
        if (!$financial) {
            return null;
        }

        // providersData structure: ['stripe' => ['account_id' => '...'], 'paypal' => ['email' => '...']]
        // Or simplified: ['stripe' => 'id', 'paypal' => 'email']?
        // Let's stick to the structure we decided: JSON with keys.
        // For Stripe Connect, we need 'account_id'.
        
        $providerData = $financial->providersData[$provider] ?? null;
        if (is_array($providerData)) {
            return $providerData['account_id'] ?? null;
        }
        
        // Handle case where it might be simple key-value if simplified later, 
        // but for now let's assume array structure for future extensibility (metadata).
        return null;
    }

    /**
     * Returns true if the mediator has a valid account configured for the provider.
     */
    public function canMediatorAcceptPayment(int $mediatorId, string $provider): bool
    {
        return $this->getProviderAccountId($mediatorId, $provider) !== null;
    }
}
