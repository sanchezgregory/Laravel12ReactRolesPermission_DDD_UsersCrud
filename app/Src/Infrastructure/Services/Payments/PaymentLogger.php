<?php

namespace App\Src\Infrastructure\Services\Payments;

use Illuminate\Support\Facades\Log;

/**
 * Payment Logger - Centralized logging for all payment transactions
 * 
 * This service provides structured logging for the entire payment lifecycle:
 * - Payment creation
 * - Gateway redirects
 * - Webhook processing
 * - Status updates
 * - Errors and failures
 */
class PaymentLogger
{
    private const CHANNEL = 'payments';

    /**
     * Log payment creation
     */
    public static function logPaymentCreated(
        int $paymentId,
        string $gateway,
        int $userId,
        ?int $mediatorId,
        int $amountMinor,
        string $currency
    ): void {
        self::log('info', 'PAYMENT_CREATED', [
            'payment_id' => $paymentId,
            'gateway' => $gateway,
            'user_id' => $userId,
            'mediator_id' => $mediatorId,
            'amount_minor' => $amountMinor,
            'currency' => $currency,
            'status' => 'pending',
        ]);
    }

    /**
     * Log gateway preference/session creation
     */
    public static function logGatewaySessionCreated(
        int $paymentId,
        string $gateway,
        string $providerSessionId,
        string $redirectUrl
    ): void {
        self::log('info', 'GATEWAY_SESSION_CREATED', [
            'payment_id' => $paymentId,
            'gateway' => $gateway,
            'provider_session_id' => $providerSessionId,
            'redirect_url' => $redirectUrl,
        ]);
    }

    /**
     * Log webhook received
     */
    public static function logWebhookReceived(
        string $gateway,
        string $eventType,
        ?string $paymentIntentId,
        array $rawData = []
    ): void {
        self::log('info', 'WEBHOOK_RECEIVED', [
            'gateway' => $gateway,
            'event_type' => $eventType,
            'payment_intent_id' => $paymentIntentId,
            'raw_data' => $rawData,
        ]);
    }

    /**
     * Log payment status fetched from gateway
     */
    public static function logPaymentStatusFetched(
        string $gateway,
        string $paymentIntentId,
        string $gatewayStatus,
        string $mappedStatus,
        ?string $externalReference = null
    ): void {
        self::log('info', 'PAYMENT_STATUS_FETCHED', [
            'gateway' => $gateway,
            'payment_intent_id' => $paymentIntentId,
            'gateway_status' => $gatewayStatus,
            'mapped_status' => $mappedStatus,
            'external_reference' => $externalReference,
        ]);
    }

    /**
     * Log payment lookup attempt
     */
    public static function logPaymentLookup(
        string $gateway,
        string $lookupMethod,
        string $identifier,
        bool $found,
        ?int $paymentId = null
    ): void {
        self::log($found ? 'info' : 'warning', 'PAYMENT_LOOKUP', [
            'gateway' => $gateway,
            'lookup_method' => $lookupMethod,
            'identifier' => $identifier,
            'found' => $found,
            'payment_id' => $paymentId,
        ]);
    }

    /**
     * Log payment status update
     */
    public static function logPaymentStatusUpdated(
        int $paymentId,
        string $gateway,
        string $previousStatus,
        string $newStatus,
        string $source, // 'webhook' or 'sync'
        ?string $paymentIntentId = null
    ): void {
        self::log('info', 'PAYMENT_STATUS_UPDATED', [
            'payment_id' => $paymentId,
            'gateway' => $gateway,
            'previous_status' => $previousStatus,
            'new_status' => $newStatus,
            'source' => $source,
            'payment_intent_id' => $paymentIntentId,
        ]);
    }

    /**
     * Log payment already in final state (idempotent webhook)
     */
    public static function logPaymentAlreadyProcessed(
        int $paymentId,
        string $gateway,
        string $currentStatus
    ): void {
        self::log('info', 'PAYMENT_ALREADY_PROCESSED', [
            'payment_id' => $paymentId,
            'gateway' => $gateway,
            'current_status' => $currentStatus,
            'note' => 'Idempotent webhook - payment already in final state',
        ]);
    }

    /**
     * Log user return from gateway
     */
    public static function logUserReturn(
        string $route,
        string $identifier,
        ?int $paymentId,
        bool $paid,
        array $queryParams = []
    ): void {
        self::log('info', 'USER_RETURN', [
            'route' => $route,
            'identifier' => $identifier,
            'payment_id' => $paymentId,
            'paid' => $paid,
            'query_params' => $queryParams,
        ]);
    }

    /**
     * Log payment sync attempt
     */
    public static function logPaymentSync(
        int $paymentId,
        string $gateway,
        ?string $syncedStatus,
        bool $updated
    ): void {
        self::log('info', 'PAYMENT_SYNC', [
            'payment_id' => $paymentId,
            'gateway' => $gateway,
            'synced_status' => $syncedStatus,
            'updated' => $updated,
        ]);
    }

    /**
     * Log payment error
     */
    public static function logPaymentError(
        string $context,
        string $message,
        array $data = [],
        ?\Throwable $exception = null
    ): void {
        $logData = array_merge([
            'context' => $context,
            'error_message' => $message,
        ], $data);

        if ($exception) {
            $logData['exception'] = [
                'class' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ];
        }

        self::log('error', 'PAYMENT_ERROR', $logData);
    }

    /**
     * Log payment warning
     */
    public static function logPaymentWarning(
        string $context,
        string $message,
        array $data = []
    ): void {
        self::log('warning', 'PAYMENT_WARNING', array_merge([
            'context' => $context,
            'warning_message' => $message,
        ], $data));
    }

    /**
     * Internal logging method
     */
    private static function log(string $level, string $event, array $context): void
    {
        $enrichedContext = array_merge([
            'event' => $event,
            'timestamp' => now()->toIso8601String(),
            'environment' => app()->environment(),
        ], $context);

        Log::channel(self::CHANNEL)->$level($event, $enrichedContext);
    }
}
