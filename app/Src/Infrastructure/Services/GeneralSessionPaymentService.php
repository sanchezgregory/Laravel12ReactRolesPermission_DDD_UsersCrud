<?php

namespace App\Src\Infrastructure\Services;

use App\Src\Application\DTO\Payments\CreateCheckoutDTO;
use App\Src\Application\DTO\Payments\CreateCheckoutResultDTO;
use App\Src\Infrastructure\Services\Payments\PaymentFactory;
use App\Src\Infrastructure\Services\Payments\PaymentLogger;
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

        PaymentLogger::logPaymentCreated(
            paymentId: $savedPayment->id,
            gateway: $gatewaySlug,
            userId: $dto->userId,
            mediatorId: $dto->mediatorId,
            amountMinor: $dto->amountMinor,
            currency: $dto->currency
        );

        // 4. Calculate Fee if any
        $marketplaceFee = 0;
        try {
            $platformFeePercent = $this->paymentConfigService->getEffectivePlatformFeePercent($dto->mediatorId);
            if ($platformFeePercent > 0) {
                 $marketplaceFee = (int) round($dto->amountMinor * ($platformFeePercent / 100));
            }
        } catch (\Exception $e) {
            PaymentLogger::logPaymentWarning(
                context: 'fee_calculation',
                message: 'Could not calculate platform fee',
                data: ['payment_id' => $savedPayment->id, 'error' => $e->getMessage()]
            );
        }
        
        // 5. Use Factory to get Gateway
        $gateway = $this->paymentFactory->make($gatewaySlug);
        
        // 6. Create Payment via Gateway
        try {
            $providerResult = $gateway->createPayment($savedPayment, $marketplaceFee);
            
            PaymentLogger::logGatewaySessionCreated(
                paymentId: $savedPayment->id,
                gateway: $gatewaySlug,
                providerSessionId: $providerResult->providerSessionId,
                redirectUrl: $providerResult->redirectUrl
            );
        } catch (\Exception $e) {
            PaymentLogger::logPaymentError(
                context: 'gateway_session_creation',
                message: 'Failed to create gateway session',
                data: ['payment_id' => $savedPayment->id, 'gateway' => $gatewaySlug],
                exception: $e
            );
            throw $e;
        }

        // 7. Update Entity with provider session ID
        $savedPayment->providerSessionId = $providerResult->providerSessionId;
        $this->repo->update($savedPayment->id, $savedPayment);

        return new CreateCheckoutResultDTO(
            paymentId: $savedPayment->id,
            redirectUrl: $providerResult->redirectUrl
        );
    }

    public function syncPaymentStatus(string $identifier): ?array
    {
        // Try multiple lookup strategies for different gateways
        // 1. First try by provider session ID (Stripe checkout session, MercadoPago preference)
        $payment = $this->repo->findByProviderSessionId($identifier);
        
        // 2. If not found and identifier is numeric, try direct ID lookup (MercadoPago external_reference)
        if (!$payment && is_numeric($identifier)) {
            Log::info("Payment not found by providerSessionId, trying direct ID lookup", ['identifier' => $identifier]);
            $payment = $this->repo->findById((int) $identifier);
        }
        
        if (!$payment) {
            Log::warning("Payment not found with identifier: {$identifier}");
            return null;
        }

        Log::info("Payment found for sync", [
            'payment_id' => $payment->id,
            'identifier_used' => $identifier,
            'current_status' => $payment->status->value
        ]);

        $gatewaySlug = $payment->metadata['gateway'] ?? 'stripe'; // Default to stripe for legacy
        
        try {
             $gateway = $this->paymentFactory->make($gatewaySlug);
             $status = $gateway->syncPayment($payment);

             if ($status && $status->value === PaymentStatus::PAID && $payment->status->value !== PaymentStatus::PAID) {
                  $payment->markPaid();
                  $this->repo->update($payment->id, $payment);
                  Log::info("Payment marked as paid via sync: " . $payment->id);
             }
             
             return [
                 'paid' => ($payment->status->value === PaymentStatus::PAID),
                 'mediator' => $payment->mediatorId,
             ];

        } catch (\Exception $e) {
            Log::error("Sync Error for payment {$payment->id}: " . $e->getMessage());
            return null;
        }
    }

    public function handleWebhook(string $gatewaySlug, \Illuminate\Http\Request $request): void
    {
        $gateway = $this->paymentFactory->make($gatewaySlug);
        $result = $gateway->handleWebhook($request);

        if (!$result->handled || !$result->providerSessionId) {
            Log::info("Webhook not handled or missing providerSessionId", [
                'gateway' => $gatewaySlug,
                'handled' => $result->handled
            ]);
            return;
        }

        // Try to find payment by providerSessionId (which is external_reference for MercadoPago)
        $payment = $this->repo->findByProviderSessionId($result->providerSessionId);
        
        Log::info("Webhook: First lookup by providerSessionId", [
            'gateway' => $gatewaySlug,
            'providerSessionId' => $result->providerSessionId,
            'found' => $payment ? 'yes' : 'no'
        ]);
        
        // Fallback: try direct ID lookup if providerSessionId is numeric
        if (!$payment && is_numeric($result->providerSessionId)) {
            Log::info("Webhook: Payment not found by providerSessionId, trying direct ID", [
                'gateway' => $gatewaySlug,
                'identifier' => $result->providerSessionId
            ]);
            $payment = $this->repo->findById((int) $result->providerSessionId);
            
            Log::info("Webhook: Direct ID lookup result", [
                'found' => $payment ? 'yes' : 'no',
                'payment_id' => $payment?->id
            ]);
        }

        if (!$payment) {
            Log::warning("Payment not found for webhook", [
                'gateway' => $gatewaySlug,
                'providerSessionId' => $result->providerSessionId,
                'paymentIntentId' => $result->paymentIntentId
            ]);
            return;
        }

        Log::info("Webhook: Payment found", [
            'gateway' => $gatewaySlug,
            'payment_id' => $payment->id,
            'current_status' => $payment->status->value,
            'new_status' => $result->status?->value,
            'result_status_object' => get_class($result->status),
            'comparison_will_be' => ($result->status && $result->status->value === PaymentStatus::PAID) ? 'TRUE - will update' : 'FALSE - will not update'
        ]);

        if ($result->status && $result->status->value === PaymentStatus::PAID) {
             if ($payment->status->value !== PaymentStatus::PAID) {
                 $payment->markPaid($result->paymentIntentId);
                 $this->repo->update($payment->id, $payment);
                 Log::info("Payment marked as paid via webhook", [
                     'payment_id' => $payment->id,
                     'gateway' => $gatewaySlug
                 ]);
             } else {
                 Log::info("Payment already marked as paid (idempotent webhook)", [
                     'payment_id' => $payment->id
                 ]);
             }
        } elseif ($result->status && $result->status->value === PaymentStatus::FAILED) {
             $payment->markFailed();
             $this->repo->update($payment->id, $payment);
             Log::info("Payment marked as failed via webhook", ['payment_id' => $payment->id]);
        } else {
            Log::warning("Webhook: Status condition not met", [
                'result_status_is_null' => $result->status === null,
                'result_status_value' => $result->status?->value,
                'expected_value' => PaymentStatus::PAID
            ]);
        }
    }
}
