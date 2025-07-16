<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Users;

use App\Src\Infrastructure\Controllers\Controller;
use App\Src\Application\Services\UserService;
use App\Src\Infrastructure\Requests\Backoffice\Users\StoreUserRequest as UsersStoreUserRequest;

class StoreController extends Controller
{
    public function __invoke(UsersStoreUserRequest $request, UserService $userService)
    {

        $userService->save($request->validated());
        return redirect()->route('admin.users.index')->with('success', 'Usuario creado exitosamente.');
    }
}
