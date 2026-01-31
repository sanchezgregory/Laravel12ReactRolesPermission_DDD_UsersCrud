<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Coupons;

use App\Src\Infrastructure\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IndexController extends Controller
{
    public function __invoke(Request $request)
    {
        $coupons = Coupon::query()
            ->with(['users:id,name,email'])
            ->withCount('redemptions')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // For user selection in the Edit Modal
        $users = \App\Models\User::role('user')->select('id', 'name', 'email')->get();

        return Inertia::render('backoffice/Coupons/Index', [
            'coupons' => $coupons,
            'users' => $users
        ]);
    }
}
