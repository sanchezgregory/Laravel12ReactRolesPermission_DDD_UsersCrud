<?php

namespace App\Src\Infrastructure\Services;

use App\Src\Application\DTO\Payments\CreateCheckoutDTO;
use App\Src\Application\DTO\Payments\CreateCheckoutResultDTO;
use App\Src\Application\DTO\Payments\ProviderCheckoutDTO;
use App\Src\Application\DTO\Payments\WebhookResultDTO;
use App\Src\Domain\Contracts\RepositoryContracts\SessionPaymentRepositoryInterface;
use App\Src\Application\Services\UserService;
use App\Src\Domain\Entities\SessionPaymentEntity;
use App\Src\Domain\ValueObjects\Money;
use App\Src\Domain\ValueObjects\PaymentMethod;
use App\Src\Domain\ValueObjects\PaymentStatus;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Stripe\Checkout\Session as StripeCheckoutSession;


class StripeSessionPaymentService
{
    public function __construct(
        private readonly SessionPaymentRepositoryInterface $repo,
        private readonly UserService $userService
    ) {}

    public function createCheckout(array $data): CreateCheckoutResultDTO
    {
        $user = $this->userService->findById($data['user_id']);

        $dto = new CreateCheckoutDTO(
            userId: $data['user_id'],
            mediatorId: $data['mediator_id'],
            email: $user->email,
            method: $data['method'],
            amountMinor: $data['amount_minor'],
            currency: $data['currency'],
            topic: $data['topic'],
        );

        // 1. Prepare Value Objects
        $money = Money::from($dto->amountMinor, $dto->currency);
        $method = PaymentMethod::fromString($dto->method);
        $status = PaymentStatus::fromString(PaymentStatus::PENDING);

        // 2. Create Entity
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
            metadata: $dto->getMetadata()
        );
        
        // 3. Save to get ID
        $savedPayment = $this->repo->save($payment);

        // 4. Create Provider Checkout
        // The provider returns a ProviderCheckoutDTO
        $providerResult = $this->createStripeCheckout($dto, $savedPayment->id);

        // 5. Update Entity with provider session ID
        $savedPayment->providerSessionId = $providerResult->providerSessionId;
        // We update the entity via repository
        $this->repo->update($savedPayment->id, $savedPayment);

        // 6. Return Result
        return new CreateCheckoutResultDTO(
            paymentId: $savedPayment->id,
            redirectUrl: $providerResult->redirectUrl
        );
    }

    // Here we resolve the webhook based on the provider, updating the payment status in DB
    public function handleWebhook(string $provider, string $payload, array $headers): void
    {
        $result = $this->resolveStripeWebhook($provider, $payload, $headers);
        
        if (!$result->handled) {
            // Not necessarily an exception, just ignored. But user code threw exception.
            // throw new InvalidArgumentException('Webhook not handled.');
            // Better to just return if not handled or strict? 
            // The controller expects void.
            return;
        }

        if (!$result->providerSessionId) {
            // Evento no relacionado a checkout.session o sin session_id
            return;
        }

        $payment = $this->repo->findByProviderSessionId($result->providerSessionId);
        if (!$payment) {
            Log::warning("Payment not found for provider session: " . $result->providerSessionId);
            return;
        }

        if ($result->status === PaymentStatus::PAID) {
            $payment->markPaid($result->paymentIntentId);
            $this->repo->update($payment->id, $payment);
            Log::info("Payment marked as paid: " . $payment->id);
            // Aquí después (fase 2): emitir evento interno -> habilitar calendly + emails
            return;
        }
        
        // Handle failed/expired if needed
    }

    private function resolveStripeWebhook(string $provider, string $payload, array $headers): WebhookResultDTO
    {
        $signature = $headers['stripe-signature'] ?? $headers['Stripe-Signature'] ?? null;
        if (!$signature) {
            return new WebhookResultDTO(false, 'missing_signature', null, null);
        }

        $secret = (string) env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent($payload, $signature, $secret);
        } catch (SignatureVerificationException $e) {
            return new WebhookResultDTO(false, 'invalid_signature', null, null, null, ['error' => $e->getMessage()]);
        } catch (\UnexpectedValueException $e) {
            return new WebhookResultDTO(false, 'invalid_payload', null, null, null, ['error' => $e->getMessage()]);
        }

        $type = (string) $event->type;

        // Nos interesa confirmar pagos reales
        if ($type === 'checkout.session.completed') {
            /** @var \Stripe\Checkout\Session $session */
            $session = $event->data->object;

            $providerSessionId = (string) $session->id;
            $paymentIntentId = isset($session->payment_intent) ? (string) $session->payment_intent : null;

            // payment_status suele ser 'paid' cuando es exitoso
            $status = PaymentStatus::FAILED;
            if (isset($session->payment_status) && (string) $session->payment_status === 'paid') {
                $status = PaymentStatus::PAID;
            }

            return new WebhookResultDTO(
                handled: true,
                eventType: $type,
                providerSessionId: $providerSessionId,
                status: $status,
                paymentIntentId: $paymentIntentId,
                meta: [
                    'client_reference_id' => isset($session->client_reference_id) ? (string) $session->client_reference_id : null,
                    'metadata' => isset($session->metadata) ? (array) $session->metadata : [],
                ]
            );
        }

        return new WebhookResultDTO(true, $type, null, null);
    }

    private function createStripeCheckout(CreateCheckoutDTO $dto, int $paymentId)
    {
        $successUrl = (string) env('PAYMENTS_SUCCESS_URL', config('app.url') . '/payments/success');
        $cancelUrl  = (string) env('PAYMENTS_CANCEL_URL', config('app.url') . '/payments/cancel');

        // Metadata clave para correlación (además del provider_session_id que guardamos)
        $metadata = array_merge($dto->getMetadata(), [
            'payment_id' => (string) $paymentId,
            'user_id' => (string) $dto->userId,
            'mediator_id' => $dto->mediatorId ? (string) $dto->mediatorId : '',
        ]);

        $session = StripeCheckoutSession::create([
            'mode' => 'payment',
            'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $cancelUrl,
            'client_reference_id' => (string) $paymentId,
            'customer_email' => $dto->email,
            'metadata' => $metadata,

            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => strtolower($dto->currency),
                    'unit_amount' => $dto->amountMinor,
                    'product_data' => [
                        'name' => $dto->topic ? "Mediation session: {$dto->topic}" : 'Mediation session',
                    ],
                ],
            ]],
        ]);

        return new ProviderCheckoutDTO(
            redirectUrl: (string) $session->url,
            providerSessionId: (string) $session->id,
            raw: $session->toArray()
        );
    }

    public function syncPaymentStatus(string $providerSessionId): ?array
    {
        try {
            // 1. Retrieve the session from Stripe
            $session = StripeCheckoutSession::retrieve($providerSessionId);
            
            // 2. Find local payment
            $payment = $this->repo->findByProviderSessionId($providerSessionId);
            if (!$payment) {
                Log::warning("Payment not found for provider session: " . $providerSessionId);
                return null;
            }

            // 3. Check status
            if ($session->payment_status === 'paid') {
                if ($payment->status->value !== PaymentStatus::PAID) {
                     $paymentIntentId = isset($session->payment_intent) ? (string) $session->payment_intent : null;
                     $payment->markPaid($paymentIntentId);
                     $this->repo->update($payment->id, $payment);
                     Log::info("Payment marked as paid via sync: " . $payment->id);
                }
                
                return [
                    'paid' => true,
                    'mediator' => $payment->mediatorId,
                ];
            }

            return [
                'paid' => false,
                'mediator' => $payment->mediatorId,
            ];

        } catch (\Exception $e) {
            Log::error("Error syncing payment status: " . $e->getMessage());
            return null;
        }
    }
}
