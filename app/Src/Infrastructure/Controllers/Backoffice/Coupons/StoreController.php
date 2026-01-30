<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Coupons;

use App\Src\Infrastructure\Controllers\Controller;
use App\Mail\CouponAssigned;
use App\Src\Application\Coupons\CouponService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    protected $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'code' => 'nullable|string|size:6|regex:/^[A-Z0-9]+$/|unique:coupons,code',
            'discount_percentage' => 'required|integer|in:25,50,75,100',
            'expires_at' => 'required|date|after:today',
            'max_uses_per_user' => 'required|integer|min:1|max:3',
            'allowed_users_type' => 'required|in:all,new_users,selected',
            'selected_users' => 'required_if:allowed_users_type,selected|array',
            'selected_users.*' => 'exists:users,id',
            'active' => 'boolean',
        ]);

        DB::transaction(function () use ($data) {
            $coupon = $this->couponService->createCoupon($data);

            // Send emails if active
            if ($coupon->active) {
                if ($coupon->allowed_users_type === 'selected') {
                    foreach ($coupon->users as $user) {
                        Mail::to($user)->queue(new CouponAssigned($coupon, $user));
                    }
                }
            }
        });

        return redirect()->route('backoffice.coupons.index')->with('success', 'Coupon created successfully.');
    }
}
