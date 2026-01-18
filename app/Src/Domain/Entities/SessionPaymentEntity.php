<?php

namespace App\Src\Domain\Entities;

use App\Src\Domain\ValueObjects\Currency;
use App\Src\Domain\ValueObjects\Money;
use App\Src\Domain\ValueObjects\PaymentMethod;
use App\Src\Domain\ValueObjects\PaymentStatus;

class SessionPaymentEntity extends BaseEntity
{
    public function __construct(
        public ?int $id,
        public int $userId,
        public ?string $email,
        public ?string $clientName = null, // Added
        public ?int $mediatorId,
        public PaymentMethod $method,
        public PaymentStatus $status,
        public Money $money,
        public ?string $providerSessionId = null,
        public ?string $providerPaymentIntentId = null,
        public ?string $topic = null,
        public array $metadata = [],
        public ?\DateTimeImmutable $scheduledAt = null,
        public ?string $meetingLink = null,
    ) {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public static function fromArray(array $data): static
    {
        $money = Money::from(
            (int) ($data['amount_total'] ?? 0),
            Currency::fromString((string) ($data['currency'] ?? 'USD'))
        );
        
        $entity = new static(
            $data['id'] ?? null,
            (int) $data['user_id'],
            $data['email'] ?? null,
            $data['client_name'] ?? null, // Added
            isset($data['mediator_id']) ? (int) $data['mediator_id'] : null,
            PaymentMethod::fromString((string) ($data['method'] ?? '')),
            PaymentStatus::fromString((string) ($data['status'] ?? PaymentStatus::PENDING)),
            $money,
            $data['provider_session_id'] ?? null,
            $data['provider_payment_intent_id'] ?? null,
            $data['topic'] ?? null,
            is_array($data['metadata'] ?? null) ? $data['metadata'] : (array) json_decode((string) ($data['metadata'] ?? '[]'), true),
            null, // scheduledAt - set below
            $data['meeting_link'] ?? null,
        );
        
        if (isset($data['scheduled_at'])) {
            $entity->scheduledAt = $data['scheduled_at'] instanceof \DateTimeImmutable 
                ? $data['scheduled_at'] 
                : new \DateTimeImmutable((string)$data['scheduled_at']);
        }

        // ... dates logic
        
        if (isset($data['created_at'])) {
            $entity->createdAt = $data['created_at'] instanceof \DateTimeImmutable 
                ? $data['created_at'] 
                : new \DateTimeImmutable((string)$data['created_at']);
        }

        if (isset($data['updated_at'])) {
            $entity->updatedAt = $data['updated_at'] instanceof \DateTimeImmutable 
                ? $data['updated_at'] 
                : new \DateTimeImmutable((string)$data['updated_at']);
        }

        return $entity;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'email' => $this->email,
            'client_name' => $this->clientName, // Added
            'mediator_id' => $this->mediatorId,
            'method' => (string) $this->method,
            'status' => (string) $this->status,
            'amount_total' => $this->money->amountMinor,
            'currency' => (string) $this->money->currency,
            'provider_session_id' => $this->providerSessionId,
            'provider_payment_intent_id' => $this->providerPaymentIntentId,
            'topic' => $this->topic,
            'metadata' => $this->metadata,
            'scheduled_at' => $this->scheduledAt ? $this->scheduledAt->format('Y-m-d H:i:s') : null,
            'meeting_link' => $this->meetingLink,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
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
