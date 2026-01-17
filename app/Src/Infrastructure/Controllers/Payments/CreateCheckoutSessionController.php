<?php

namespace App\Src\Infrastructure\Controllers\Payments;

use App\Src\Infrastructure\Requests\Payments\CreateCheckoutSessionRequest;
use App\Src\Infrastructure\Services\StripeSessionPaymentService;

class CreateCheckoutSessionController
{
    public function __construct(private readonly StripeSessionPaymentService $service) {}

    public function __invoke(CreateCheckoutSessionRequest $request): \Symfony\Component\HttpFoundation\Response
    {
        \Illuminate\Support\Facades\Log::info('CreateCheckoutSessionController invoked', $request->all());
        $result = $this->service->createCheckout($request->toArray());

        return \Inertia\Inertia::location($result->redirectUrl);
    }
}
