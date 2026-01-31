<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\UserSpace;

use App\Src\Infrastructure\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MyCouponsController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        // Get coupons available for the user
        $coupons = Coupon::availableForUser($user)
            ->withCount(['redemptions as user_redemptions' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->get();
        
        return Inertia::render('backoffice/UserSpace/MyCoupons', [
            'coupons' => $coupons
        ]);
    }
}
