<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Users;

use App\Src\Domain\Contracts\ServiceContracts\UserServiceInterface;
use App\Src\Infrastructure\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;


class EditController extends Controller
{
    public function __invoke(int $id, UserServiceInterface $userService): Response
    {
        $user = $userService->findById($id);
        return Inertia::render('backoffice/users/Edit', [
            'user' => $user,
            'roles' => Role::all()->pluck('name'),
        ]);
    }
}
