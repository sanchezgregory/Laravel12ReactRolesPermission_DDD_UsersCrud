<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SessionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mediator_id',
        'method',
        'status',
        'amount_total',
        'currency',
        'provider_session_id',
        'provider_payment_intent_id',
        'topic',
        'metadata',
        'scheduled_at',
        'meeting_link',
    ];

    protected $casts = [
        'amount_total' => 'integer',
        'metadata' => 'array',
        'scheduled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mediator()
    {
        return $this->belongsTo(User::class, 'mediator_id');
    }
}
