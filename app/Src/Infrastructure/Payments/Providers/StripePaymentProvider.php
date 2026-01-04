<?php

namespace App\Src\Infrastructure\Payments\Providers;

use App\Src\Application\DTO\Payments\CreateCheckoutDTO;
use App\Src\Application\DTO\Payments\ProviderCheckoutDTO;
use App\Src\Application\DTO\Payments\WebhookResultDTO;
use App\Src\Domain\Contracts\PaymentContracts\PaymentProviderInterface;
use App\Src\Domain\ValueObjects\PaymentMethod;
use Stripe\Checkout\Session as StripeCheckoutSession;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;

class StripePaymentProvider implements PaymentProviderInterface
{
    public function __construct()
    {
        Stripe::setApiKey((string) config('services.stripe.secret', env('STRIPE_SECRET')));
    }

    public function method(): PaymentMethod
    {
        return PaymentMethod::fromString(PaymentMethod::STRIPE);
    }

    public function createCheckout(CreateCheckoutDTO $dto, int $paymentId): ProviderCheckoutDTO
    {
        $successUrl = (string) env('PAYMENTS_SUCCESS_URL', config('app.url') . '/payments/success');
        $cancelUrl  = (string) env('PAYMENTS_CANCEL_URL', config('app.url') . '/payments/cancel');

        // Metadata clave para correlación (además del provider_session_id que guardamos)
        $metadata = array_merge($dto->metadata, [
            'payment_id' => (string) $paymentId,
            'user_id' => (string) $dto->userId,
            'mediator_id' => $dto->mediatorId ? (string) $dto->mediatorId : '',
        ]);

        $session = StripeCheckoutSession::create([
            'mode' => 'payment',
            'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $cancelUrl,
            'client_reference_id' => (string) $paymentId,

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

    public function handleWebhook(string $payload, array $headers): WebhookResultDTO
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

            // payment_status suele ser 'paid' cuando completó exitoso
            $status = null;
            if (isset($session->payment_status) && (string) $session->payment_status === 'paid') {
                $status = 'paid';
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

        // Otros eventos los puedes mapear luego
        return new WebhookResultDTO(true, $type, null, null);
    }
}
