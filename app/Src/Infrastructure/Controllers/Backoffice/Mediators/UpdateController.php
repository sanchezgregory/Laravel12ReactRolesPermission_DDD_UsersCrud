<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Mediators;

use App\Src\Application\Services\MediatorService;
use App\Src\Infrastructure\Controllers\Controller;
use App\Src\Infrastructure\Requests\Backoffice\Mediators\UpdateMediatorRequest;

class UpdateController extends Controller
{
    public function __invoke(int $id, UpdateMediatorRequest $request, MediatorService $service)
    {
        $service->update($id, $request->toArray());

        return redirect()
            ->route('backoffice.mediators.index')
            ->with('success', 'Mediator updated successfully.');
    }
}
