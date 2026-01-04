<?php

namespace App\Src\Domain\Entities;

class MediatorEntity extends BaseEntity
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public int $sessionPriceMinor,
        public string $currency,
        public ?string $calendlyUrl = null,
        public ?string $headline = null,
        public ?string $bio = null,
    ) {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public static function fromArray(array $data): static
    {
        return new static(
            id: (int) $data['id'],
            name: (string) ($data['name'] ?? ''),
            email: (string) ($data['email'] ?? ''),
            sessionPriceMinor: (int) ($data['session_price_minor'] ?? 0),
            currency: (string) ($data['currency'] ?? 'EUR'),
            calendlyUrl: $data['calendly_url'] ?? null,
            headline: $data['headline'] ?? null,
            bio: $data['bio'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'session_price_minor' => $this->sessionPriceMinor,
            'currency' => $this->currency,
            'calendly_url' => $this->calendlyUrl,
            'headline' => $this->headline,
            'bio' => $this->bio,
        ];
    }
}
