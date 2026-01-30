<?php

namespace App\Src\Application\Coupons;

use App\Models\Coupon;
use App\Models\CouponRedemption;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;

class CouponService
{
    /**
     * Generate a unique 6-character coupon code (uppercase alphanumeric).
     */
    public function generateCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (Coupon::where('code', $code)->exists());

        return $code;
    }

    /**
     * Create a new coupon.
     */
    public function createCoupon(array $data): Coupon
    {
        // Handle code generation if not provided
        if (empty($data['code'])) {
            $data['code'] = $this->generateCode();
        } else {
            $data['code'] = strtoupper($data['code']);
        }

        $coupon = Coupon::create([
            'code' => $data['code'],
            'discount_percentage' => $data['discount_percentage'],
            'expires_at' => $data['expires_at'],
            'max_uses_per_user' => $data['max_uses_per_user'] ?? 1,
            'allowed_users_type' => $data['allowed_users_type'] ?? 'all',
            'active' => $data['active'] ?? true,
        ]);

        if (isset($data['selected_users']) && $data['allowed_users_type'] === 'selected') {
            $coupon->users()->sync($data['selected_users']);
        }

        return $coupon;
    }

    /**
     * Validate if a user can use a coupon.
     * Returns the Coupon model if valid, or throws an Exception with the reason.
     */
    public function validateCoupon(string $code, User $user): Coupon
    {
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            throw new Exception("Coupon not found.");
        }

        if (!$coupon->active) {
            throw new Exception("Coupon is inactive.");
        }

        if ($coupon->isExpired()) {
            throw new Exception("Coupon has expired.");
        }

        // Check audience
        if ($coupon->allowed_users_type === 'selected') {
            if (!$coupon->users()->where('user_id', $user->id)->exists()) {
                throw new Exception("This coupon is not valid for your account.");
            }
        } elseif ($coupon->allowed_users_type === 'new_users') {
            // Logic for new users, e.g., registered in last 7 days or no orders.
            // For now, assuming simply verifying creation date vs coupon start or simple check.
            // Let's implement a check: if created_at is older than 30 days, invalid.
            if ($user->created_at->lt(Carbon::now()->subDays(30))) {
                throw new Exception("This coupon is only for new users.");
            }
        }

        // Check usage limit
        $usageCount = CouponRedemption::where('user_id', $user->id)
            ->where('coupon_id', $coupon->id)
            ->count();

        if ($usageCount >= $coupon->max_uses_per_user) {
            throw new Exception("You have reached the maximum usage limit for this coupon.");
        }

        return $coupon;
    }

    /**
     * Redeem a coupon for a user.
     */
    public function redeemCoupon(string $code, User $user): CouponRedemption
    {
        $coupon = $this->validateCoupon($code, $user);

        return CouponRedemption::create([
            'coupon_id' => $coupon->id,
            'user_id' => $user->id,
            'redeemed_at' => now(),
        ]);
    }
}
