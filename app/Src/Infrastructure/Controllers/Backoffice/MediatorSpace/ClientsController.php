<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\MediatorSpace;

use App\Src\Application\Services\SessionPaymentService;
use App\Src\Infrastructure\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClientsController extends Controller
{
    public function __invoke(SessionPaymentService $service)
    {
        $clients = $service->getClientsByMediatorId(Auth::id());
        
        return Inertia::render('backoffice/mediator-space/clients', [
            'clients' => $clients,
        ]);
    }
}
