<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\MediatorSpace;

use App\Src\Application\Services\SessionPaymentService;
use App\Src\Infrastructure\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PaymentsController extends Controller
{
    public function __invoke(SessionPaymentService $service)
    {
        // Fetch entities and map to array for view if necessary, or use toArray() on entities
        $payments = $service->getByMediatorId(Auth::id());
        
        $paymentsData = array_map(function($payment) {
            return $payment->toArray();
        }, $payments);

        return Inertia::render('backoffice/mediator-space/payments', [
            'payments' => $paymentsData,
        ]);
    }
}
