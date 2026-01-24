# Integración de PaymentLogger en GeneralSessionPaymentService

Este documento describe cómo integrar el PaymentLogger en los métodos existentes.

## Cambios en syncPaymentStatus()

Reemplazar los `Log::info()` y `Log::warning()` existentes con llamadas a `PaymentLogger`:

```php
public function syncPaymentStatus(string $identifier): ?array
{
    // Try multiple lookup strategies for different gateways
    // 1. First try by provider session ID (Stripe checkout session, MercadoPago preference)
    $payment = $this->repo->findByProviderSessionId($identifier);
    
    PaymentLogger::logPaymentLookup(
        gateway: 'unknown',
        lookupMethod: 'provider_session_id',
        identifier: $identifier,
        found: $payment !== null,
        paymentId: $payment?->id
    );
    
    // 2. If not found and identifier is numeric, try direct ID lookup (MercadoPago external_reference)
    if (!$payment && is_numeric($identifier)) {
        $payment = $this->repo->findById((int) $identifier);
        
        PaymentLogger::logPaymentLookup(
            gateway: 'unknown',
            lookupMethod: 'direct_id',
            identifier: $identifier,
            found: $payment !== null,
            paymentId: $payment?->id
        );
    }
    
    if (!$payment) {
        PaymentLogger::logPaymentWarning(
            context: 'sync_payment_status',
            message: 'Payment not found',
            data: ['identifier' => $identifier]
        );
        return null;
    }

    $gatewaySlug = $payment->metadata['gateway'] ?? 'stripe';
    
    try {
         $gateway = $this->paymentFactory->make($gatewaySlug);
         $status = $gateway->syncPayment($payment);

         $updated = false;
         if ($status && $status->value === PaymentStatus::PAID && $payment->status->value !== PaymentStatus::PAID) {
              $previousStatus = $payment->status->value;
              $payment->markPaid();
              $this->repo->update($payment->id, $payment);
              $updated = true;
              
              PaymentLogger::logPaymentStatusUpdated(
                  paymentId: $payment->id,
                  gateway: $gatewaySlug,
                  previousStatus: $previousStatus,
                  newStatus: PaymentStatus::PAID,
                  source: 'sync'
              );
         }
         
         PaymentLogger::logPaymentSync(
             paymentId: $payment->id,
             gateway: $gatewaySlug,
             syncedStatus: $status?->value,
             updated: $updated
         );
         
         return [
             'paid' => ($payment->status->value === PaymentStatus::PAID),
             'mediator' => $payment->mediatorId,
         ];

    } catch (\Exception $e) {
        PaymentLogger::logPaymentError(
            context: 'sync_payment_status',
            message: 'Sync error',
            data: ['payment_id' => $payment->id, 'gateway' => $gatewaySlug],
            exception: $e
        );
        return null;
    }
}
```

## Cambios en handleWebhook()

Similar approach - reemplazar logs existentes:

```php
public function handleWebhook(string $gatewaySlug, \Illuminate\Http\Request $request): void
{
    $gateway = $this->paymentFactory->make($gatewaySlug);
    $result = $gateway->handleWebhook($request);

    PaymentLogger::logWebhookReceived(
        gateway: $gatewaySlug,
        eventType: $result->eventType,
        paymentIntentId: $result->paymentIntentId,
        rawData: $request->all()
    );

    if (!$result->handled || !$result->providerSessionId) {
        PaymentLogger::logPaymentWarning(
            context: 'webhook_handler',
            message: 'Webhook not handled or missing providerSessionId',
            data: [
                'gateway' => $gatewaySlug,
                'handled' => $result->handled,
                'event_type' => $result->eventType
            ]
        );
        return;
    }

    // Try to find payment
    $payment = $this->repo->findByProviderSessionId($result->providerSessionId);
    
    PaymentLogger::logPaymentLookup(
        gateway: $gatewaySlug,
        lookupMethod: 'provider_session_id',
        identifier: $result->providerSessionId,
        found: $payment !== null,
        paymentId: $payment?->id
    );
    
    // Fallback: try direct ID lookup
    if (!$payment && is_numeric($result->providerSessionId)) {
        $payment = $this->repo->findById((int) $result->providerSessionId);
        
        PaymentLogger::logPaymentLookup(
            gateway: $gatewaySlug,
            lookupMethod: 'direct_id',
            identifier: $result->providerSessionId,
            found: $payment !== null,
            paymentId: $payment?->id
        );
    }

    if (!$payment) {
        PaymentLogger::logPaymentWarning(
            context: 'webhook_handler',
            message: 'Payment not found',
            data: [
                'gateway' => $gatewaySlug,
                'providerSessionId' => $result->providerSessionId,
                'paymentIntentId' => $result->paymentIntentId
            ]
        );
        return;
    }

    if ($result->status && $result->status->value === PaymentStatus::PAID) {
         if ($payment->status->value !== PaymentStatus::PAID) {
             $previousStatus = $payment->status->value;
             $payment->markPaid($result->paymentIntentId);
             $this->repo->update($payment->id, $payment);
             
             PaymentLogger::logPaymentStatusUpdated(
                 paymentId: $payment->id,
                 gateway: $gatewaySlug,
                 previousStatus: $previousStatus,
                 newStatus: PaymentStatus::PAID,
                 source: 'webhook',
                 paymentIntentId: $result->paymentIntentId
             );
         } else {
             PaymentLogger::logPaymentAlreadyProcessed(
                 paymentId: $payment->id,
                 gateway: $gatewaySlug,
                 currentStatus: $payment->status->value
             );
         }
    } elseif ($result->status && $result->status->value === PaymentStatus::FAILED) {
         $previousStatus = $payment->status->value;
         $payment->markFailed();
         $this->repo->update($payment->id, $payment);
         
         PaymentLogger::logPaymentStatusUpdated(
             paymentId: $payment->id,
             gateway: $gatewaySlug,
             previousStatus: $previousStatus,
             newStatus: PaymentStatus::FAILED,
             source: 'webhook'
         );
    }
}
```

## Integración en MercadoPagoService

Agregar logging en el método `handleWebhook`:

```php
use App\Src\Infrastructure\Services\Payments\PaymentLogger;

public function handleWebhook(Request $request): WebhookResultDTO
{
    $id = $request->input('data.id') ?? $request->input('id');
    $type = $request->input('type') ?? $request->input('topic');

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
                providerSessionId: $payment->external_reference,
                status: $status,
                paymentIntentId: (string) $payment->id,
                meta: (array) $payment
            );

        } catch (\Exception $e) {
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
```

## Integración en PaymentReturnController

```php
use App\Src\Infrastructure\Services\Payments\PaymentLogger;

public function __invoke(Request $request)
{
    $identifier = $request->input('session_id')
               ?? $request->input('preference_id')
               ?? $request->input('id');

    if (!$identifier) {
        return redirect()->route('mediators.index')->with('error', 'No payment identifier provided.');
    }

    $paymentData = $this->service->syncPaymentStatus((string) $identifier);
    
    PaymentLogger::logUserReturn(
        route: $request->route()->getName(),
        identifier: $identifier,
        paymentId: $paymentData['mediator'] ?? null,
        paid: $paymentData['paid'] ?? false,
        queryParams: $request->all()
    );

    // ... rest of the method
}
```
