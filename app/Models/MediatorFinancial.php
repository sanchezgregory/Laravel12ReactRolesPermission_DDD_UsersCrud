<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediatorFinancial extends Model
{
    protected $fillable = [
        'user_id',
        'custom_platform_fee_percent',
        'providers_data',
    ];

    protected $casts = [
        'custom_platform_fee_percent' => 'integer',
        'providers_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
