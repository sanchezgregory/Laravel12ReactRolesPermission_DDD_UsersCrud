<?php

namespace App\Src\Application\Services\Payments;

use App\Src\Application\DTO\Payments\CreateCheckoutDTO;
use App\Src\Application\DTO\Payments\CreateCheckoutResultDTO;
use App\Src\Domain\Contracts\PaymentContracts\PaymentProviderResolverInterface;
use App\Src\Domain\Contracts\RepositoryContracts\SessionPaymentRepositoryInterface;
use App\Src\Domain\Contracts\ServiceContracts\SessionPaymentServiceInterface;
use App\Src\Domain\Entities\SessionPaymentEntity;
use App\Src\Domain\ValueObjects\Money;
use App\Src\Domain\ValueObjects\PaymentMethod;
use App\Src\Domain\ValueObjects\PaymentStatus;
use InvalidArgumentException;

class SessionPaymentService implements SessionPaymentServiceInterface
{
    public function __construct(
        private readonly SessionPaymentRepositoryInterface $payments,
        private readonly PaymentProviderResolverInterface $resolver,
    ) {}

    public function createCheckout(CreateCheckoutDTO $dto): CreateCheckoutResultDTO
    {
        $method = PaymentMethod::fromString($dto->method);

        // 1) Persistimos pending
        $entity = new SessionPaymentEntity(
            id: null,
            userId: $dto->userId,
            mediatorId: $dto->mediatorId,
            method: $method,
            status: PaymentStatus::fromString(PaymentStatus::PENDING),
            money: Money::from($dto->amountMinor, $dto->currency),
            providerSessionId: null,
            providerPaymentIntentId: null,
            topic: $dto->topic,
            metadata: $dto->metadata,
        );

        $saved = $this->payments->save($entity);

        // 2) Strategy
        $provider = $this->resolver->resolve($method);

        // 3) Create provider checkout
        $checkout = $provider->createCheckout($dto, (int) $saved->id);

        // 4) Guardamos provider session id
        $saved->providerSessionId = $checkout->providerSessionId;
        $this->payments->update((int) $saved->id, $saved);

        return new CreateCheckoutResultDTO(
            paymentId: (int) $saved->id,
            redirectUrl: $checkout->redirectUrl,
        );
    }

    public function handleWebhook(string $method, string $payload, array $headers): void
    {
        $paymentMethod = PaymentMethod::fromString($method);

        $provider = $this->resolver->resolve($paymentMethod);
        $result = $provider->handleWebhook($payload, $headers);

        if (!$result->handled) {
            throw new InvalidArgumentException('Webhook not handled.');
        }

        if (!$result->providerSessionId) {
            // Evento no relacionado a checkout.session o sin session_id
            return;
        }

        $payment = $this->payments->findByProviderSessionId($result->providerSessionId);
        if (!$payment) {
            // No existe en DB => ignorar (o log)
            return;
        }

        if ($result->status === 'paid') {
            $payment->markPaid($result->paymentIntentId);
            $this->payments->update((int) $payment->id, $payment);

            // Aquí después (fase 2): emitir evento interno -> habilitar calendly + emails
            return;
        }

        // Si quieres mapear failed/expired en V1, añade eventos Stripe y actualiza aquí
    }
}
