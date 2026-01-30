<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Coupons;

use App\Src\Infrastructure\Controllers\Controller;
use App\Models\Coupon;

class DestroyController extends Controller
{
    public function __invoke($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return redirect()->route('backoffice.coupons.index')->with('success', 'Coupon deleted successfully.');
    }
}
