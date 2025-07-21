<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Users;

use App\Src\Domain\Contracts\ServiceContracts\UserServiceInterface;
use App\Src\Infrastructure\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __invoke(UserServiceInterface $userService): Response
    {
        $users = $userService->getAll();
        return Inertia::render('backoffice/users/Index', [
            'users' => $users,
        ]);
    }
}
