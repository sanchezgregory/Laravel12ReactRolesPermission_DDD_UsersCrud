<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Users;

use App\Src\Infrastructure\Controllers\Controller;
use App\Src\Application\Services\UserService;

class DestroyController extends Controller
{
    public function __invoke(UserService $userService, int $id)
    {
        $user = $userService->findById($id);
        $userService->delete($user);
        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}
