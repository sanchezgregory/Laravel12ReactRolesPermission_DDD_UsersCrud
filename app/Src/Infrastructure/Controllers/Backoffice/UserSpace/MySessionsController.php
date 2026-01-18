<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\UserSpace;

use App\Src\Application\Services\SessionPaymentService;
use App\Src\Infrastructure\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MySessionsController extends Controller
{
    public function __invoke(SessionPaymentService $service)
    {
        $sessionsData = $service->getAllSessionsByUserId(Auth::id());

        return Inertia::render('backoffice/user-space/my-sessions', [
            'sessions' => $sessionsData,
        ]);
    }
}
