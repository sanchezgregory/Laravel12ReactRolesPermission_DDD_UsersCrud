<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Users;

use App\Src\Domain\Contracts\RepositoryContracts\RoleRepositoryInterface;
use App\Src\Domain\Contracts\ServiceContracts\UserServiceInterface;
use App\Src\Infrastructure\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class EditController extends Controller
{
    public function __invoke(int $id, UserServiceInterface $userService, RoleRepositoryInterface $roleService): Response
    {

        $user = $userService->findById($id);
        $roles = $roleService->getAll();

        return Inertia::render('backoffice/users/Edit', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }
}
