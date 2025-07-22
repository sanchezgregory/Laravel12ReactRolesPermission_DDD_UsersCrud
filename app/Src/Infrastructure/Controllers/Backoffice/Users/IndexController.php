<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Users;

use App\Src\Domain\Contracts\ServiceContracts\UserServiceInterface;
use App\Src\Infrastructure\Controllers\Controller;
use App\Src\Infrastructure\Resources\UserResource;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __invoke(UserServiceInterface $userService): Response
    {
        $userEntities = $userService->getAll();
        return Inertia::render('backoffice/users/Index', [
            'users' => UserResource::collection($userEntities),
        ]);
    }
}
