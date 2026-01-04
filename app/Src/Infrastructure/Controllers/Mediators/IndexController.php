<?php

namespace App\Src\Infrastructure\Controllers\Mediators;

use App\Src\Domain\Contracts\ServiceContracts\MediatorServiceInterface;
use App\Src\Infrastructure\Controllers\Controller;
use Inertia\Inertia;

class IndexController extends Controller
{
    public function __invoke(MediatorServiceInterface $mediatorService)
    {
        $mediators = $mediatorService->getAll();

        return Inertia::render('Mediators/Index', [
            'mediators' => array_map(fn($m) => $m->toArray(), $mediators),
        ]);
    }
}
