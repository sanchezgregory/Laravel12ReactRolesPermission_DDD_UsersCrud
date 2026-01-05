<?php

namespace App\Src\Infrastructure\Controllers\Mediators;

use App\Src\Application\Services\MediatorService;
use App\Src\Infrastructure\Controllers\Controller;
use Inertia\Inertia;

class IndexController extends Controller
{
    public function __invoke(MediatorService $mediatorService)
    {
        $mediators = $mediatorService->getAll();

        return Inertia::render('mediators/Index', [
            'mediators' => array_map(fn($m) => $m->toArray(), $mediators),
        ]);
    }
}
