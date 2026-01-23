<?php

namespace App\Src\Domain\Contracts\Payments;

use App\Src\Domain\Entities\SessionPaymentEntity;
use App\Src\Application\DTO\Payments\ProviderCheckoutDTO;
use App\Src\Application\DTO\Payments\WebhookResultDTO;
use App\Src\Domain\ValueObjects\PaymentStatus;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    public function createPayment(SessionPaymentEntity $order, ?int $marketplaceFee = 0): ProviderCheckoutDTO;
    
    public function handleWebhook(Request $request): WebhookResultDTO;
    
    public function syncPayment(SessionPaymentEntity $payment): ?PaymentStatus;
    
    public function refund(string $paymentId): void;
}
