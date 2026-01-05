<?php

namespace App\Src\Infrastructure\Controllers\Payments;

use App\Src\Infrastructure\Services\StripeSessionPaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StripePaymentWebhookController
{
    public function __construct(private readonly StripeSessionPaymentService $service) {}

    public function __invoke(Request $request): Response
    {
        // Stripe requiere el payload RAW para validar firma
        $payload = $request->getContent();
       
        // Headers normalizados
        $headers = [];
        foreach ($request->headers->all() as $k => $v) {
            $headers[$k] = is_array($v) ? implode(',', $v) : (string) $v;
        }

        // Handle webhook, updating payment status in DB
        $this->service->handleWebhook("stripe", $payload, $headers);

        return response('ok', 200);
    }
}
