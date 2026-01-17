<?php

namespace App\Src\Infrastructure\Controllers\Mediators;

use App\Src\Application\Services\MediatorService;
use App\Src\Infrastructure\Controllers\Controller;
use Inertia\Inertia;

use App\Src\Application\Services\SessionPaymentService;
use Illuminate\Support\Facades\Auth;

class ShowController extends Controller
{
    public function __invoke(int $id, MediatorService $mediatorService, SessionPaymentService $sessionPaymentService)
    {
        $mediator = $mediatorService->findById($id);

        if (!$mediator) {
            abort(404, 'Mediador no encontrado');
        }

        $hasActivePayment = false;
        $otherActiveSession = null;

        if (Auth::check()) {
            $hasActivePayment = $sessionPaymentService->hasActivePayment(Auth::id(), $id);

            // Check if user has active session with ANOTHER mediator
            if (!$hasActivePayment) {
                $activeSessions = $sessionPaymentService->getActiveSessionsByUserId(Auth::id());
                foreach ($activeSessions as $session) {
                    if ($session['mediator_id'] !== $id) {
                        $otherActiveSession = $session;
                        break; // Just need one to warn
                    }
                }
            }
        }

        return Inertia::render('mediators/Show', [
            'mediator' => $mediator->toArray(),
            'has_active_payment' => $hasActivePayment,
            'other_active_session' => $otherActiveSession,
        ]);
    }
}
