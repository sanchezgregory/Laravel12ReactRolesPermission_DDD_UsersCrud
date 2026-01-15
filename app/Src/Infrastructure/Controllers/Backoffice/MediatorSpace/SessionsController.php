<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\MediatorSpace;

use App\Src\Application\Services\SessionPaymentService;
use App\Src\Infrastructure\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SessionsController extends Controller
{
    public function __invoke(SessionPaymentService $service)
    {
        // Re-using payments as sessions for now, logic can be refined if sessions are separate entities
        $payments = $service->getByMediatorId(Auth::id());
        
        $sessionsData = array_map(function($payment) {
            return $payment->toArray(); 
        }, $payments);

        return Inertia::render('backoffice/mediator-space/sessions', [
            'sessions' => $sessionsData,
        ]);
    }
}
