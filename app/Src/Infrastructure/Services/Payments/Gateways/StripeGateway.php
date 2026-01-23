<?php

namespace App\Src\Infrastructure\Services\Payments\Gateways;

use App\Src\Domain\Contracts\Payments\PaymentGatewayInterface;
use App\Src\Domain\Entities\SessionPaymentEntity;
use App\Src\Application\DTO\Payments\ProviderCheckoutDTO;
use App\Src\Application\DTO\Payments\WebhookResultDTO;
use App\Src\Domain\ValueObjects\PaymentStatus;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeCheckoutSession;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeGateway implements PaymentGatewayInterface
{
    public function __construct(?array $config = null)
    {
        if ($config && isset($config['secret_key'])) {
            Stripe::setApiKey($config['secret_key']);
        } elseif (env('STRIPE_SECRET')) {
            Stripe::setApiKey(env('STRIPE_SECRET'));
        }
    }

    public function createPayment(SessionPaymentEntity $order, ?int $marketplaceFee = 0): ProviderCheckoutDTO
    {
        $successUrl = route('payments.success') . '?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl  = route('payments.cancel');

        $metadata = array_merge($order->metadata, [
            'payment_id' => (string) $order->id,
            'user_id' => (string) $order->userId,
            'mediator_id' => $order->mediatorId ? (string) $order->mediatorId : '',
        ]);
        
        // Build session params
        $sessionParams = [
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'client_reference_id' => (string) $order->id,
            'customer_email' => $order->email,
            'metadata' => $metadata,
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => strtolower($order->money->currency->value ?? 'usd'),
                    'unit_amount' => $order->money->amountMinor,
                    'product_data' => [
                        'name' => $order->topic ? "Mediation session: {$order->topic}" : 'Mediation session',
                    ],
                ],
            ]],
        ];
        
        // Fee Split Logic
        // In the original code, it retrieved connect account id from `PaymentConfigurationService`.
        // Ideally, this Gateway should receive the "destination" account ID if needed, or lookup.
        // If marketplaceFee is passed, it assumes we are charging a fee on an amount going to someone else?
        // Or is the Gateway agnostic of WHO?
        // The original code used `payment_intent_data` with `transfer_data`.
        
        if ($marketplaceFee > 0 && isset($order->metadata['stripe_account_id'])) {
             // If we have a connected account to transfer to
             $sessionParams['payment_intent_data'] = [
                'application_fee_amount' => $marketplaceFee,
                'transfer_data' => [
                    'destination' => $order->metadata['stripe_account_id'],
                ],
            ];
        }

        $session = StripeCheckoutSession::create($sessionParams);

        return new ProviderCheckoutDTO(
            redirectUrl: (string) $session->url,
            providerSessionId: (string) $session->id,
            raw: $session->toArray()
        );
    }

    public function handleWebhook(Request $request): WebhookResultDTO
    {
         $payload = $request->getContent();
         $sigHeader = $request->header('Stripe-Signature');
         $endpointSecret = env('STRIPE_WEBHOOK_SECRET'); // Or from config
         
         try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
         } catch(SignatureVerificationException $e) {
             return new WebhookResultDTO(false, 'invalid_signature', null, null);
         } catch(\UnexpectedValueException $e) {
             return new WebhookResultDTO(false, 'invalid_payload', null, null);
         }
         
         if ($event->type === 'checkout.session.completed') {
             $session = $event->data->object;
             $status = ($session->payment_status === 'paid') ? PaymentStatus::PAID : PaymentStatus::FAILED;
             
             return new WebhookResultDTO(
                handled: true,
                eventType: $event->type,
                providerSessionId: $session->id,
                status: $status,
                paymentIntentId: $session->payment_intent ?? null,
                meta: (array) $session
             );
         }
         
         return new WebhookResultDTO(true, $event->type, null, null);
    }

    public function syncPayment(SessionPaymentEntity $payment): ?PaymentStatus
    {
        try {
            $session = StripeCheckoutSession::retrieve($payment->providerSessionId);
            if ($session->payment_status === 'paid') {
                return PaymentStatus::fromString(PaymentStatus::PAID);
            }
            if ($session->status === 'expired') {
                 return PaymentStatus::fromString(PaymentStatus::EXPIRED);
            }
        } catch (\Exception $e) {
            // Log::error("Stripe Sync Error: " . $e->getMessage());
        }
        return null;
    }
    
    public function refund(string $paymentId): void
    {
        // Implementation
        // \Stripe\Refund::create(['payment_intent' => ...]);
    }
}
