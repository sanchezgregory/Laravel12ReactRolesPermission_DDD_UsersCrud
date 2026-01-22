<?php

namespace App\Src\Infrastructure\Controllers\Mediators;

use App\Src\Application\Services\MediatorService;
use App\Src\Infrastructure\Controllers\Controller;
use Inertia\Inertia;

use App\Src\Application\Services\SessionPaymentService;
use Illuminate\Support\Facades\Auth;

class ShowController extends Controller
{
    public function __invoke(
        int $id, 
        MediatorService $mediatorService, 
        SessionPaymentService $sessionPaymentService,
        \App\Src\Application\Services\PaymentConfigurationService $paymentConfigService
    ) {
        $mediator = $mediatorService->findById($id);

        if (!$mediator) {
            abort(404, 'Mediador no encontrado');
        }

        $currentSession = null;
        $otherActiveSession = null;

        if (Auth::check()) {
            $activeSessions = $sessionPaymentService->getActiveSessionsByUserId(Auth::id());
            
            foreach ($activeSessions as $session) {
                if ($session['mediator_id'] === $id) {
                    $currentSession = $session;
                } else {
                    // Just need one to warn
                    $otherActiveSession = $session;
                }
            }
        }

        $availablePaymentMethods = [];
        // Map providers to frontend method names
        if ($paymentConfigService->canMediatorAcceptPayment($id, 'stripe')) {
            $availablePaymentMethods[] = 'stripe';
        }
        if ($paymentConfigService->canMediatorAcceptPayment($id, 'paypal')) {
            $availablePaymentMethods[] = 'paypal';
        }

        return Inertia::render('mediators/Show', [
            'mediator' => $mediator->toArray(),
            'current_session' => $currentSession,
            'other_active_session' => $otherActiveSession,
            'available_payment_methods' => $availablePaymentMethods,
        ]);
    }
}
