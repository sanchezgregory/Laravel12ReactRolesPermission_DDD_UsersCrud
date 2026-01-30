<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Coupons;

use App\Src\Infrastructure\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    public function __invoke(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $data = $request->validate([
            'discount_percentage' => 'required|integer|in:25,50,75,100',
            'expires_at' => 'required|date|after:today',
            'max_uses_per_user' => 'required|integer|min:1|max:3',
            'allowed_users_type' => 'required|in:all,new_users,selected',
            'selected_users' => 'required_if:allowed_users_type,selected|array',
            'selected_users.*' => 'exists:users,id',
            'active' => 'boolean',
        ]);

        $coupon->update([
            'discount_percentage' => $data['discount_percentage'],
            'expires_at' => $data['expires_at'],
            'max_uses_per_user' => $data['max_uses_per_user'],
            'allowed_users_type' => $data['allowed_users_type'],
            'active' => $data['active'] ?? true,
        ]);

        if ($data['allowed_users_type'] === 'selected') {
            $coupon->users()->sync($data['selected_users']);
        } else {
            $coupon->users()->detach();
        }

        return redirect()->route('backoffice.coupons.index')->with('success', 'Coupon updated successfully.');
    }
}
