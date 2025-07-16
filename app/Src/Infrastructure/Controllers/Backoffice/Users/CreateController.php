<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Users;

use App\Src\Infrastructure\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class CreateController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Backoffice/Users/Create', [
            'roles' => Role::all()->pluck('name'),
        ]);
    }
}
