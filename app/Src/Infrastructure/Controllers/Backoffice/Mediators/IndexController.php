<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Mediators;

use App\Src\Application\Services\MediatorService;
use App\Src\Infrastructure\Controllers\Controller;
use Inertia\Inertia;

class IndexController extends Controller
{
    public function __invoke(MediatorService $service)
    {
        return Inertia::render('backoffice/mediators/index', [
            'mediators' => $service->getAll(),
        ]);
    }
}
