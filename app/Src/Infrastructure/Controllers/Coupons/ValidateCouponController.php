<?php

namespace App\Src\Infrastructure\Controllers\Coupons;

use App\Models\User;
use App\Src\Application\Coupons\CouponService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ValidateCouponController
{
    public function __construct(
        private readonly CouponService $couponService
    ) {}

    public function __invoke(Request $request)
    {
        $request->validate([
            'coupon_code' => ['required', 'string'],
        ]);

        try {
            /** @var User $user */
            $user = Auth::user();
            $code = $request->input('coupon_code');

            $coupon = $this->couponService->validateCoupon($code, $user);

            return response()->json([
                'valid' => true,
                'coupon' => [
                    'code' => $coupon->code,
                    'discount_percentage' => $coupon->discount_percentage,
                ],
                'message' => 'Coupon applied successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
