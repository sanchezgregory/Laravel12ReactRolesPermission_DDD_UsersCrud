<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_payment_id',
        'email',
    ];

    public function sessionPayment()
    {
        return $this->belongsTo(SessionPayment::class);
    }
}
