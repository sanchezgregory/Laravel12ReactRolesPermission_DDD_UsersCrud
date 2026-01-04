<?php

namespace App\Src\Domain\Entities;

use App\Src\Domain\ValueObjects\Money;
use App\Src\Domain\ValueObjects\PaymentMethod;
use App\Src\Domain\ValueObjects\PaymentStatus;

class SessionPaymentEntity extends BaseEntity
{
    public function __construct(
        public ?int $id,
        public int $userId,
        public ?int $mediatorId,
        public PaymentMethod $method,
        public PaymentStatus $status,
        public Money $money,
        public ?string $providerSessionId = null,
        public ?string $providerPaymentIntentId = null,
        public ?string $topic = null,
        public array $metadata = [],
    ) {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public static function fromArray(array $data): static
    {
        $money = Money::from(
            (int) ($data['amount_total'] ?? 0),
            (string) ($data['currency'] ?? 'USD')
        );

        return new static(
            $data['id'] ?? null,
            (int) $data['user_id'],
            isset($data['mediator_id']) ? (int) $data['mediator_id'] : null,
            PaymentMethod::fromString((string) ($data['method'] ?? '')),
            PaymentStatus::fromString((string) ($data['status'] ?? PaymentStatus::PENDING)),
            $money,
            $data['provider_session_id'] ?? null,
            $data['provider_payment_intent_id'] ?? null,
            $data['topic'] ?? null,
            is_array($data['metadata'] ?? null) ? $data['metadata'] : (array) json_decode((string) ($data['metadata'] ?? '[]'), true),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'mediator_id' => $this->mediatorId,
            'method' => (string) $this->method,
            'status' => (string) $this->status,
            'amount_total' => $this->money->amountMinor,
            'currency' => (string) $this->money->currency,
            'provider_session_id' => $this->providerSessionId,
            'provider_payment_intent_id' => $this->providerPaymentIntentId,
            'topic' => $this->topic,
            'metadata' => $this->metadata,
        ];
    }

    public function markPaid(?string $paymentIntentId = null): void
    {
        $this->status = PaymentStatus::fromString(PaymentStatus::PAID);
        if ($paymentIntentId) {
            $this->providerPaymentIntentId = $paymentIntentId;
        }
    }

    public function markFailed(): void
    {
        $this->status = PaymentStatus::fromString(PaymentStatus::FAILED);
    }

    public function markExpired(): void
    {
        $this->status = PaymentStatus::fromString(PaymentStatus::EXPIRED);
    }
}
