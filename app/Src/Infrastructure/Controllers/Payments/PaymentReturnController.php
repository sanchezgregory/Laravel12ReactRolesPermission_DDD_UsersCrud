<?php

namespace App\Src\Infrastructure\Controllers\Payments;

use App\Src\Infrastructure\Services\GeneralSessionPaymentService;
use App\Src\Infrastructure\Services\Payments\PaymentLogger;
use App\Src\Application\Services\UserService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class PaymentReturnController
{
    public function __construct(
        private readonly GeneralSessionPaymentService $service, 
        private readonly UserService $userService
    ) {}

    public function __invoke(Request $request)
    {
        // Accept multiple parameter names for different gateways
        $identifier = $request->input('session_id')       // Stripe checkout session
                   ?? $request->input('preference_id')    // MercadoPago preference
                   ?? $request->input('id');              // Direct payment ID
        
        Log::info('PaymentReturnController invoked', [
            'route' => $request->route()->getName(),
            'identifier' => $identifier,
            'all_params' => $request->all()
        ]);

        if (!$identifier) {
            return redirect()->route('mediators.index')->with('error', 'No payment identifier provided.');
        }

        $paymentData = $this->service->syncPaymentStatus((string) $identifier);
        
        Log::info('PaymentReturnController - Payment data retrieved', [
            'identifier' => $identifier,
            'paid' => $paymentData['paid'] ?? false
        ]);
        
        PaymentLogger::logUserReturn(
            route: $request->route()->getName(),
            identifier: $identifier,
            paymentId: $paymentData ? ($paymentData['mediator'] ?? null) : null,
            paid: $paymentData['paid'] ?? false,
            queryParams: $request->all()
        );

        if (!$paymentData || !$paymentData['paid']) {
            // If it's the cancel route or payment not successful
            if ($request->routeIs('payments.cancel')) {
                return redirect()->route('mediators.index')->with('info', 'Payment cancelled.');
            }
            
            if ($request->routeIs('payments.pending')) {
                return redirect()->route('mediators.index')->with('info', 'Payment is pending confirmation.');
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
