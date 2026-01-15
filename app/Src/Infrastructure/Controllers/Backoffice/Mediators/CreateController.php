<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Mediators;

use App\Src\Infrastructure\Controllers\Controller;
use Inertia\Inertia;

class CreateController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('backoffice/mediators/create');
    }
}
