<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Users;

use App\Src\Domain\Contracts\ServiceContracts\UserServiceInterface;
use App\Src\Infrastructure\Controllers\Controller;
use App\Src\Infrastructure\Requests\Backoffice\Users\StoreUserRequest as UsersStoreUserRequest;

class StoreController extends Controller
{
    public function __invoke(UsersStoreUserRequest $request, UserServiceInterface $userService)
    {
        $userService->save($request->validated());
        return redirect()->route('admin.users.index')->with('success', 'Usuario creado exitosamente.');
    }
}
