<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Users;

use App\Src\Domain\Contracts\RepositoryContracts\RoleRepositoryInterface;
use App\Src\Infrastructure\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class CreateController extends Controller
{
    public function __invoke(RoleRepositoryInterface $roleService): Response
    {
        $roles = $roleService->getAll();
        return Inertia::render('backoffice/users/Create', [
            'roles' => $roles,
        ]);
    }
}
