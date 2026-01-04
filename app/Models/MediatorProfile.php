<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MediatorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_price_minor',
        'currency',
        'calendly_url',
        'headline',
        'bio',
    ];

    protected $casts = [
        'session_price_minor' => 'integer',
    ];
}
