<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Coupons;

use App\Src\Infrastructure\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;

class CreateController extends Controller
{
    public function __invoke()
    {
        // For user selection list. In production with many users, this should be an async search.
        // For now, loading users for the selection list.
        $users = User::role('user')->select('id', 'name', 'email')->get();

        return Inertia::render('backoffice/Coupons/Create', [
            'users' => $users
        ]);
    }
}
