<?php

namespace App\Src\Infrastructure\Services;

use App\Src\Application\DTO\Payments\CreateCheckoutDTO;
use App\Src\Application\DTO\Payments\CreateCheckoutResultDTO;
use App\Src\Infrastructure\Services\Payments\PaymentFactory;
use App\Src\Domain\Contracts\RepositoryContracts\SessionPaymentRepositoryInterface;
use App\Src\Application\Services\UserService;
use App\Src\Application\Services\PaymentConfigurationService;
use App\Src\Domain\Entities\SessionPaymentEntity;
use App\Src\Domain\ValueObjects\Money;
use App\Src\Domain\ValueObjects\PaymentMethod;
use App\Src\Domain\ValueObjects\PaymentStatus;
use Illuminate\Support\Facades\Log;

class GeneralSessionPaymentService
{
    public function __construct(
        private readonly SessionPaymentRepositoryInterface $repo,
        private readonly UserService $userService,
        // private readonly PaymentConfigurationService $paymentConfigService, // Logic moved to Gateways or Factory? 
        // Config Service logic (platform fee check) might still be relevant for getting the FEE AMOUNT to pass to gateway.
        private readonly PaymentConfigurationService $paymentConfigService, 
        private readonly PaymentFactory $paymentFactory
    ) {}

    public function createCheckout(array $data, string $gatewaySlug): CreateCheckoutResultDTO
    {
        $user = $this->userService->findById($data['user_id']);

        $dto = new CreateCheckoutDTO(
            userId: $data['user_id'],
            mediatorId: $data['mediator_id'],
            email: $user->email,
            method: $data['method'], // 'card', etc.
            amountMinor: $data['amount_minor'],
            currency: $data['currency'],
            topic: $data['topic'],
        );

        // 1. Prepare Value Objects
        $money = Money::from($dto->amountMinor, $dto->currency);
        $method = PaymentMethod::fromString($dto->method);
        $status = PaymentStatus::fromString(PaymentStatus::PENDING);

        // 2. Create Entity
        $metadata = array_merge($dto->getMetadata(), ['gateway' => $gatewaySlug]);
        
        $payment = new SessionPaymentEntity(
            id: null,
            userId: $dto->userId,
            email: $user->email,
            clientName: $user->name,
            mediatorId: $dto->mediatorId,
            method: $method,
            status: $status,
            money: $money,
            topic: $dto->topic,
            metadata: $metadata
        );
        
        // 3. Save to get ID
        $savedPayment = $this->repo->save($payment);

        // 4. Calculate Fee if any
        $marketplaceFee = 0;
        try {
            $platformFeePercent = $this->paymentConfigService->getEffectivePlatformFeePercent($dto->mediatorId);
            if ($platformFeePercent > 0) {
                 $marketplaceFee = (int) round($dto->amountMinor * ($platformFeePercent / 100));
            }
        } catch (\Exception $e) {
            // Log::warning("Could not calculate fee: " . $e->getMessage());
        }
        
        // 5. Use Factory to get Gateway
        $gateway = $this->paymentFactory->make($gatewaySlug);
        
        // 6. Create Payment via Gateway
        $providerResult = $gateway->createPayment($savedPayment, $marketplaceFee);

        // 7. Update Entity with provider session ID
        $savedPayment->providerSessionId = $providerResult->providerSessionId;
        $this->repo->update($savedPayment->id, $savedPayment);

        return new CreateCheckoutResultDTO(
            paymentId: $savedPayment->id,
            redirectUrl: $providerResult->redirectUrl
        );
    }

    public function syncPaymentStatus(string $providerSessionId): ?array
    {
        $payment = $this->repo->findByProviderSessionId($providerSessionId);
        if (!$payment) {
            return null;
        }

        $gatewaySlug = $payment->metadata['gateway'] ?? 'stripe'; // Default to stripe for legacy
        
        try {
             $gateway = $this->paymentFactory->make($gatewaySlug);
             // Verify if gateway supports syncing? Interface has it now.
             $status = $gateway->syncPayment($payment);

             if ($status && $status->value === PaymentStatus::PAID && $payment->status->value !== PaymentStatus::PAID) {
                  $payment->markPaid(); // If we had paymentIntentId, we would pass it. 
                  // But syncPayment returns Status object only? 
                  // Interface: syncPayment(SessionPaymentEntity $payment): ?PaymentStatus;
                  // If we want to capture Intent ID, maybe return DTO or update payment inside gateway?
                  // Interface says syncPayment(Entity). Gateway could update Entity?
                  // But usually Gateway shouldn't persist.
                  // For now, simple markPaid.
                  $this->repo->update($payment->id, $payment);
                  Log::info("Payment marked as paid via sync: " . $payment->id);
             }
             
             return [
                 'paid' => ($payment->status->value === PaymentStatus::PAID),
                 'mediator' => $payment->mediatorId,
             ];

        } catch (\Exception $e) {
            Log::error("Sync Error: " . $e->getMessage());
            return null;
        }
    }

    public function handleWebhook(string $gatewaySlug, \Illuminate\Http\Request $request): void
    {
        $gateway = $this->paymentFactory->make($gatewaySlug);
        $result = $gateway->handleWebhook($request);

        if (!$result->handled || !$result->providerSessionId) {
            return;
        }

        $payment = $this->repo->findByProviderSessionId($result->providerSessionId);
        
        // Fallback search by metadata if needed (e.g. if providerSessionId changed or differs)
        if (!$payment && !empty($result->meta['payment_id'])) {
             $payment = $this->repo->findById((int)$result->meta['payment_id']);
        }

        if (!$payment) {
            Log::warning("Payment not found for webhook: {$gatewaySlug} - ID: " . $result->providerSessionId);
            return;
        }

        if ($result->status === PaymentStatus::PAID) {
             if ($payment->status->value !== PaymentStatus::PAID) {
                 $payment->markPaid($result->paymentIntentId);
                 $this->repo->update($payment->id, $payment);
                 Log::info("Payment marked as paid via webhook: " . $payment->id);
             }
        } elseif ($result->status === PaymentStatus::FAILED) {
             $payment->markFailed();
             $this->repo->update($payment->id, $payment);
        }
        // Handle other statuses if needed
    }
}
