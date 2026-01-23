<?php

namespace App\Src\Infrastructure\Controllers\Payments;

use App\Src\Infrastructure\Services\GeneralSessionPaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PaymentWebhookController
{
    public function __construct(private readonly GeneralSessionPaymentService $service) {}

    public function handleStripe(Request $request): JsonResponse
    {
        try {
            $this->service->handleWebhook('stripe', $request);
        } catch (\Exception $e) {
            // Logged inside service usually, but catch here to avoid 500 if wanted
        }
        return response()->json(['status' => 'received']);
    }

    public function handleMercadoPago(Request $request): JsonResponse
    {
        try {
            $this->service->handleWebhook('mercadopago', $request);
        } catch (\Exception $e) {
            
        }
        return response()->json(['status' => 'received']);
    }
}
