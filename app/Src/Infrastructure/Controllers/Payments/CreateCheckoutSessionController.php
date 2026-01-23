<?php

namespace App\Src\Infrastructure\Controllers\Payments;

use App\Src\Infrastructure\Requests\Payments\CreateCheckoutSessionRequest;
use App\Src\Infrastructure\Services\GeneralSessionPaymentService;

class CreateCheckoutSessionController
{
    public function __construct(private readonly GeneralSessionPaymentService $service) {}

    public function __invoke(CreateCheckoutSessionRequest $request): \Symfony\Component\HttpFoundation\Response
    {
        \Illuminate\Support\Facades\Log::info('CreateCheckoutSessionController invoked', $request->all());
        
        try {
            $gatewaySlug = $request->input('gateway');
            $result = $this->service->createCheckout($request->toArray(), $gatewaySlug);

            return \Inertia\Inertia::location($result->redirectUrl);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in CreateCheckoutSessionController: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
            
            return back()->withErrors(['error' => 'Error processing payment: ' . $e->getMessage()]);
        }
    }
}
