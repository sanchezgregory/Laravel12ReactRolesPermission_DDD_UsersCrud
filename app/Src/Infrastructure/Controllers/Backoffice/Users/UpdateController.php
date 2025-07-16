<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Users;

use App\Src\Infrastructure\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class UpdateController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Backoffice/Users/Update');
    }
}
