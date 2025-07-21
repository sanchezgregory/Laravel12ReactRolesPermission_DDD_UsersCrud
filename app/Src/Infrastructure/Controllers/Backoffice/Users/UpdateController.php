<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Users;

use App\Src\Domain\Contracts\ServiceContracts\UserServiceInterface;
use App\Src\Domain\Entities\UserEntity;
use App\Src\Infrastructure\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    public function __invoke(Request $request, int $userId, UserServiceInterface $userService): Response
    {
        $userEntity = UserEntity::fromRequest($request->validated());
        $userService->update($userId, $userEntity);
        return Inertia::render('Backoffice/Users/Update');
    }
}
