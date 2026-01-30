<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Coupons;

use App\Src\Infrastructure\Controllers\Controller;
use App\Models\Coupon;
use App\Models\User;
use Inertia\Inertia;

class EditController extends Controller
{
    public function __invoke($id)
    {
        $coupon = Coupon::with('users:id,name,email')->findOrFail($id);
        $users = User::select('id', 'name', 'email')->get();

        return Inertia::render('Backoffice/Coupons/Edit', [
            'coupon' => $coupon,
            'users' => $users
        ]);
    }
}
