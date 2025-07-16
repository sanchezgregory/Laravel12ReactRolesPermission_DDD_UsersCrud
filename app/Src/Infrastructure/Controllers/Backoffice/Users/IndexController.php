<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Users;

use App\Src\Application\Services\UserService;
use App\Src\Infrastructure\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __invoke(UserService $userService): Response
    {
        $users = $userService->getAll();
        return Inertia::render('backoffice/users/Index', [
            'users' => $users,
        ]);
    }
}
