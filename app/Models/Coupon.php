<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_percentage',
        'expires_at',
        'max_uses_per_user',
        'allowed_users_type',
        'active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'active' => 'boolean',
        'discount_percentage' => 'integer',
        'max_uses_per_user' => 'integer',
    ];

    /**
     * Users specifically allowed to use this coupon (if allowed_users_type is 'selected').
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'coupon_user');
    }

    /**
     * Redemptions of this coupon.
     */
    public function redemptions()
    {
        return $this->hasMany(CouponRedemption::class);
    }

    /**
     * Scope a query to only include active coupons.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Check if code is expired.
     */
    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    /**
     * Scope to find coupons available for a specific user.
     */
    public function scopeAvailableForUser($query, User $user)
    {
        return $query->where('active', true)
            ->where(function ($q) use ($user) {
                $q->where('allowed_users_type', 'all')
                  ->orWhere(function ($sq) use ($user) {
                      $sq->where('allowed_users_type', 'selected')
                         ->whereHas('users', function ($uq) use ($user) {
                             $uq->where('users.id', $user->id);
                         });
                  })
                  ->orWhere(function ($sq) use ($user) {
                       $sq->where('allowed_users_type', 'new_users')
                          ->whereRaw("? <= ?", [$user->created_at, now()->subDays(30)]); // Example logic: new within 30 days? 
                          // Actually "new users" usually means user.created_at >= now - 30 days.
                          // So if user was created > 30 days ago, they are NOT new.
                          // Logic: user.created_at >= NOW() - 30 days.
                          // So: '$user->created_at' >= Carbon::now()->subDays(30)
                          // SQL: NOT (created_at < now-30)
                       // Let's rely on PHP filtering for 'new_users' to be precise with Carbon, 
                       // or simple SQL: allowed_users_type = 'new_users' (we filter later or here).
                       // Let's just include them in query and filter in application if needed, OR:
                       // If we define "new user" as created in last 30 days:
                       if ($user->created_at >= now()->subDays(30)) {
                           $sq->where('allowed_users_type', 'new_users');
                       } else {
                           $sq->whereRaw('1 = 0'); // False
                       }
                  });
            });
    }
}
