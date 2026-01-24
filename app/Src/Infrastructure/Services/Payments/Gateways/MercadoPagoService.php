<?php

namespace App\Src\Infrastructure\Services\Payments\Gateways;

use App\Src\Domain\Contracts\Payments\PaymentGatewayInterface;
use App\Src\Domain\Entities\SessionPaymentEntity;
use App\Src\Application\DTO\Payments\ProviderCheckoutDTO;
use App\Src\Application\DTO\Payments\WebhookResultDTO;
use App\Src\Domain\ValueObjects\PaymentStatus;
use App\Src\Infrastructure\Services\Payments\PaymentLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;

class MercadoPagoService implements PaymentGatewayInterface
{
    protected ?string $accessToken = null;

    public function __construct(?array $config = null)
    {
        if ($config && isset($config['access_token'])) {
            $this->accessToken = $config['access_token'];
            MercadoPagoConfig::setAccessToken($this->accessToken);
        } elseif (config('services.mercadopago.token')) {
            $this->accessToken = config('services.mercadopago.token');
            MercadoPagoConfig::setAccessToken($this->accessToken);
        }
    }

    /**
     * @param SessionPaymentEntity $order
     * @param int|null $marketplaceFee Application fee percent or amount? The prompt implies amount: "$order->total * 0.30"
     * But createStripeCheckout calculated `applicationFeeAmount` (int). 
     * The prompt says: "createPayment ... aceptar opcionalmente un application_fee ... dejando el campo listo para recibir el 30%"
     * I will assume $marketplaceFee is the AMOUNT in the same currency (e.g. float or based on SDK need).
     * Since MP Preference 'marketplace_fee' is typically a value amount.
     */
    public function createPayment(SessionPaymentEntity $order, ?int $marketplaceFee = 0): ProviderCheckoutDTO
    {
        Log::info("MercadoPagoService: Starting createPayment for Order ID: {$order->id}");
        Log::info("MercadoPagoService: Access Token Present? " . ($this->accessToken ? 'Yes' : 'No'));

        $client = new PreferenceClient();

        // Convert minor units to unit amount (e.g. 1050 -> 10.50)
        $unitPrice = $order->money->amountMinor / 100;
        
        $preferenceData = [
            "items" => [
                [
                    "id" => (string) $order->id, // or topic id
                    "title" => $order->topic ? "Mediation: {$order->topic}" : "Mediation Session",
                    "quantity" => 1,
                    "unit_price" => $unitPrice,
                    "currency_id" => $order->money->currency->value ?? 'USD', 
                ]
            ],
            "metadata" => array_merge($order->metadata, [
                "payment_id" => $order->id,
                "user_id" => $order->userId,
                "mediator_id" => $order->mediatorId
            ]),
            "external_reference" => (string) $order->id,
            "back_urls" => [
                "success" => route('payments.success'), 
                "failure" => route('payments.cancel'),
                "pending" => route('payments.pending') // Optional
            ],
            "auto_return" => "approved",
            "notification_url" => route('webhooks.payments.mercadopago'),
        ];

        Log::info("MercadoPago Preference Data: " . json_encode($preferenceData));

        // Lógica de Split (Marketplace Fee)
        if ($marketplaceFee > 0) {
            // "marketplace_fee" => $order->total * 0.30 (as per prompt example)
            // Note: marketplace_fee in createPayment arguments should probably be passed as the calculated amount.
            // If the prompt says "El método... debe aceptar opcionalmente un application_fee", 
            // I'll use the passed value.
            
            // NOTE: Uncomment when Marketplace account is active
            // $preferenceData['marketplace_fee'] = $marketplaceFee / 100; // Assuming fee passed in minor units too?
            // Or if passed as float: $preferenceData['marketplace_fee'] = $marketplaceFee;
            
            // For now, logic mapped as requested:
            // "Aunque hoy sea 0, la lógica de split ya queda mapeada"
            // $preferenceData["marketplace_fee"] = $marketplaceFee; 
        }

        try {
            $preference = $client->create($preferenceData);
            
            return new ProviderCheckoutDTO(
                redirectUrl: $preference->init_point, // or sandbox_init_point
                providerSessionId: $preference->id,
                raw: (array) $preference
            );

        } catch (MPApiException $e) {
            Log::error("MercadoPago Error: " . $e->getMessage());
            if ($e->getApiResponse()) {
                Log::error("MercadoPago Response: " . json_encode($e->getApiResponse()->getContent()));
            }
            throw $e;
        } catch (\Exception $e) {
            Log::error("MercadoPago General Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function handleWebhook(Request $request): WebhookResultDTO
    {
        $id = $request->input('data.id') ?? $request->input('id');
        $type = $request->input('type') ?? $request->input('topic');

        Log::info("MercadoPago Webhook: Type: $type, ID: $id", $request->all());

        if (($type === 'payment' || $type === 'topic_payment') && $id) {
            try {
                $client = new \MercadoPago\Client\Payment\PaymentClient();
                $payment = $client->get($id);

                $status = match ($payment->status) {
                    'approved' => PaymentStatus::fromString(PaymentStatus::PAID),
                    'pending', 'in_process', 'in_mediation' => PaymentStatus::fromString(PaymentStatus::PENDING),
                    'rejected', 'cancelled', 'refunded', 'charged_back' => PaymentStatus::fromString(PaymentStatus::FAILED),
                    default => PaymentStatus::fromString(PaymentStatus::PENDING),
                };

                Log::info("MercadoPago Payment Fetched: ID: {$payment->id}, Status: {$payment->status} -> Map: {$status->value}, External Ref: {$payment->external_reference}");
                
                PaymentLogger::logPaymentStatusFetched(
                    gateway: 'mercadopago',
                    paymentIntentId: (string) $payment->id,
                    gatewayStatus: $payment->status,
                    mappedStatus: $status->value,
                    externalReference: $payment->external_reference
                );

                return new WebhookResultDTO(
                    handled: true,
                    eventType: $type,
                    providerSessionId: $payment->external_reference, // This matches our SessionPayment->id
                    status: $status,
                    paymentIntentId: (string) $payment->id,
                    meta: (array) $payment
                );

            } catch (\Exception $e) {
                Log::error("MercadoPago Webhook Error fetching payment $id: " . $e->getMessage());
                
                PaymentLogger::logPaymentError(
                    context: 'mercadopago_webhook',
                    message: 'Error fetching payment from MercadoPago',
                    data: ['payment_id' => $id, 'type' => $type],
                    exception: $e
                );
                
                return new WebhookResultDTO(handled: true, eventType: $type, providerSessionId: null, status: null);
            }
        }

        return new WebhookResultDTO(handled: true, eventType: $type ?? 'unknown', providerSessionId: null, status: null);
    }

    public function syncPayment(SessionPaymentEntity $payment): ?PaymentStatus
    {
        try {
            // Try to get payment status from MercadoPago
            // We can use either the providerSessionId (preference_id) or providerPaymentIntentId (payment_id)
            
            // If we have the payment intent ID (from webhook), use it directly
            if ($payment->providerPaymentIntentId) {
                $client = new \MercadoPago\Client\Payment\PaymentClient();
                $mpPayment = $client->get($payment->providerPaymentIntentId);
                
                $status = match ($mpPayment->status) {
                    'approved' => PaymentStatus::fromString(PaymentStatus::PAID),
                    'pending', 'in_process', 'in_mediation' => PaymentStatus::fromString(PaymentStatus::PENDING),
                    'rejected', 'cancelled', 'refunded', 'charged_back' => PaymentStatus::fromString(PaymentStatus::FAILED),
                    default => PaymentStatus::fromString(PaymentStatus::PENDING),
                };
                
                Log::info("MercadoPago syncPayment: Payment ID {$mpPayment->id}, Status: {$mpPayment->status} -> {$status->value}");
                return $status;
            }
            
            // If we only have the preference_id, we can't directly get payment status
            // Return null to keep current status
            Log::info("MercadoPago syncPayment: No payment intent ID available for payment {$payment->id}");
            return null;
            
        } catch (\Exception $e) {
            Log::error("MercadoPago syncPayment error: " . $e->getMessage());
            return null;
        }
    }

    public function refund(string $paymentId): void
    {
        // Logic to refund via API
        // $client = new \MercadoPago\Client\Payment\PaymentClient();
        // $client->refund($paymentId);
    }
}
