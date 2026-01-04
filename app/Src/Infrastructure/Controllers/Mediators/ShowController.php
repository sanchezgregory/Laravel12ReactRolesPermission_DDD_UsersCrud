<?php

namespace App\Src\Infrastructure\Controllers\Mediators;

use App\Src\Domain\Contracts\ServiceContracts\MediatorServiceInterface;
use App\Src\Infrastructure\Controllers\Controller;
use Inertia\Inertia;

class ShowController extends Controller
{
    public function __invoke(int $id, MediatorServiceInterface $mediatorService)
    {
        $mediator = $mediatorService->findById($id);

        if (!$mediator) {
            abort(404, 'Mediador no encontrado');
        }

        return Inertia::render('Mediators/Show', [
            'mediator' => $mediator->toArray(),
        ]);
    }
}
