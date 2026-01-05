<?php

namespace App\Src\Infrastructure\Controllers\Mediators;

use App\Src\Application\Services\MediatorService;
use App\Src\Infrastructure\Controllers\Controller;
use Inertia\Inertia;

class ShowController extends Controller
{
    public function __invoke(int $id, MediatorService $mediatorService)
    {
        $mediator = $mediatorService->findById($id);

        if (!$mediator) {
            abort(404, 'Mediador no encontrado');
        }

        return Inertia::render('mediators/Show', [
            'mediator' => $mediator->toArray(),
        ]);
    }
}
