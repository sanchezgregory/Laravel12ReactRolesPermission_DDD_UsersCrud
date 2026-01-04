<?php

namespace App\Src\Infrastructure\Controllers\Payments;

use App\Src\Domain\Contracts\ServiceContracts\SessionPaymentServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentWebhookController
{
    public function __construct(private readonly SessionPaymentServiceInterface $service) {}

    public function __invoke(string $method, Request $request): Response
    {
        // Stripe requiere el payload RAW para validar firma
        $payload = $request->getContent();

        // Headers normalizados
        $headers = [];
        foreach ($request->headers->all() as $k => $v) {
            $headers[$k] = is_array($v) ? implode(',', $v) : (string) $v;
        }

        $this->service->handleWebhook($method, $payload, $headers);

        return response('ok', 200);
    }
}
