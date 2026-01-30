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
            ->withCount('redemptions')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Backoffice/Coupons/Index', [
            'coupons' => $coupons
        ]);
    }
}
