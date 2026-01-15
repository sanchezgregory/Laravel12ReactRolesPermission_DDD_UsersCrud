<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Mediators;

use App\Src\Application\Services\MediatorService;
use App\Src\Infrastructure\Controllers\Controller;
use App\Src\Infrastructure\Requests\Backoffice\Mediators\StoreMediatorRequest;

class StoreController extends Controller
{
    public function __invoke(StoreMediatorRequest $request, MediatorService $service)
    {
        $service->save($request->toArray());

        return redirect()
            ->route('backoffice.mediators.index')
            ->with('success', 'Mediator created successfully.');
    }
}
