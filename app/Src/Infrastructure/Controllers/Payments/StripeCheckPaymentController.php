<?php

namespace App\Src\Infrastructure\Controllers\Payments;

use App\Src\Application\Services\SessionPaymentService;
use App\Src\Application\Services\UserService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class StripeCheckPaymentController
{
    public function __construct(private readonly SessionPaymentService $service, private readonly UserService $userService) {}

    public function __invoke(Request $request)
    {
        $paymentData = $this->service->checkStatusPayment($request->all());
        Log::info('StripeCheckPaymentController', ['paymentData' => json_encode($paymentData)]);

        if (!$paymentData) {
            return redirect()->route('mediators.index')->with('error', 'Payment failed!');
        }

        $calendlyUrl = $this->userService->getCalendlyUrl($paymentData['mediator']);

        return redirect()->route('mediators.show', [
            'id' => $paymentData['mediator'],
            'calendly_url' => $calendlyUrl,
        ])->with('success', 'Payment successful!');
    }
}
