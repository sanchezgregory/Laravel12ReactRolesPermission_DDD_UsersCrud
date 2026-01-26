<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MediatorSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'mediator_id',
        'name',
        'category',
        'cost',
        'currency',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'cost' => 'integer',
    ];

    public function mediator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mediator_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SessionPayment::class);
    }
}
