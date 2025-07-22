<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Users;

use App\Src\Domain\Contracts\ServiceContracts\UserServiceInterface;
use App\Src\Domain\Entities\UserEntity;
use App\Src\Infrastructure\Controllers\Controller;
use App\Src\Infrastructure\Requests\Backoffice\Users\StoreUserRequest;

class StoreController extends Controller
{
    public function __invoke(StoreUserRequest $request, UserServiceInterface $userService)
    {
        $userService->save($request->toArray());
        return redirect()->route('backoffice.users.index')->with('success', 'Usuario creado exitosamente.');
    }
}
