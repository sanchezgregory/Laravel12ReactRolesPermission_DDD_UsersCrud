<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Mediators;

use App\Src\Application\Services\MediatorService;
use App\Src\Infrastructure\Controllers\Controller;

class DestroyController extends Controller
{
    public function __invoke(int $id, MediatorService $service)
    {
        $service->delete($id);

        return redirect()
            ->route('backoffice.mediators.index')
            ->with('success', 'Mediator deleted successfully.');
    }
}
