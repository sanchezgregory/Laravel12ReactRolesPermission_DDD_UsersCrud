<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Mediators;

use App\Src\Application\Services\MediatorService;
use App\Src\Infrastructure\Controllers\Controller;
use Inertia\Inertia;

class EditController extends Controller
{
    public function __invoke(int $id, MediatorService $service)
    {
        $mediator = $service->findById($id);

        if (!$mediator) {
            abort(404);
        }

        return Inertia::render('backoffice/mediators/edit', [
            'mediator' => $mediator->toArray(),
        ]);
    }
}
