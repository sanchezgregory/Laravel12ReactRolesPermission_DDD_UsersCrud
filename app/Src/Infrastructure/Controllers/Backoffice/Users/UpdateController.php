<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Users;

use App\Src\Domain\Contracts\ServiceContracts\UserServiceInterface;
use App\Src\Domain\Entities\UserEntity;
use App\Src\Infrastructure\Controllers\Controller;
use App\Src\Infrastructure\Requests\Backoffice\Users\UpdateUserRequest;
use Illuminate\Http\RedirectResponse;

class UpdateController extends Controller
{
    public function __invoke(UpdateUserRequest $request, int $userId, UserServiceInterface $userService): RedirectResponse
    {
        $userEntity = UserEntity::fromRequest($request->toArray());
        $userService->update($userId, $userEntity);
        return redirect()->route('backoffice.users.index')->with('success', 'Usuario actualizado exitosamente.');
    }
}
