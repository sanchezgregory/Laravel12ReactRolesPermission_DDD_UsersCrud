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
                    $otherActiveSession = $session;
                }
            }
        }

        // Get active gateways from DB
        $activeGateways = \Illuminate\Support\Facades\DB::table('payment_gateways')
            ->where('is_active', true)
            ->pluck('slug')
            ->toArray();

        $availablePaymentMethods = [];
        
        foreach ($activeGateways as $slug) {
            if ($slug === 'stripe') {
                // Stripe requires Mediator connect account
                if ($paymentConfigService->canMediatorAcceptPayment($id, 'stripe')) {
                    $availablePaymentMethods[] = 'stripe';
                }
            } elseif ($slug === 'mercadopago') {
                // Mercado Pago (MVP): Assumes platform account or generic setup.
                // If you want to enforce mediator config for MP later, add check here.
                // For now, if active globally, it's available.
                $availablePaymentMethods[] = 'mercadopago';
            }
        }

        return Inertia::render('mediators/Show', [
            'mediator' => $mediator->toArray(),
            'current_session' => $currentSession,
            'other_active_session' => $otherActiveSession,
            'available_payment_methods' => $availablePaymentMethods,
        ]);
    }
}
