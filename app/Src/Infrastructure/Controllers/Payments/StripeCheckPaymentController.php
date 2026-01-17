<?php

namespace App\Src\Infrastructure\Controllers\Payments;

use App\Src\Infrastructure\Services\StripeSessionPaymentService;
use App\Src\Application\Services\UserService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class StripeCheckPaymentController
{
    public function __construct(
        private readonly StripeSessionPaymentService $service, 
        private readonly UserService $userService
    ) {}

    public function __invoke(Request $request)
    {
        $sessionId = $request->input('session_id');
        
        Log::info('Requests: StripeCheckPaymentController', ['session_id' => $sessionId]);

        if (!$sessionId) {
            return redirect()->route('mediators.index')->with('error', 'No session ID provided.');
        }

        $paymentData = $this->service->syncPaymentStatus((string) $sessionId);
        Log::info('Response: StripeCheckPaymentController', ['paymentData' => json_encode($paymentData)]);

        if (!$paymentData || !$paymentData['paid']) {
            // If it's the cancel route or payment not successful
            if ($request->routeIs('payments.cancel')) {
                return redirect()->route('mediators.index')->with('info', 'Payment cancelled.');
            }
            return redirect()->route('mediators.index')->with('error', 'Payment validation failed or not completed.');
        }

        $calendlyUrl = $this->userService->getCalendlyUrl($paymentData['mediator']);

        return redirect()->route('mediators.show', [
            'id' => $paymentData['mediator'],
            'calendly_url' => $calendlyUrl,
        ])->with('success', 'Payment successful!');
    }
}
