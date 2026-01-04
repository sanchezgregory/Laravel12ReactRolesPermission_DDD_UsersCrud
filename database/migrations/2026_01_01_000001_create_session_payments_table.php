<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Por ahora nullable para que no te bloquee si el módulo de mediadores no está aún.
            $table->unsignedBigInteger('mediator_id')->nullable();
            $table->index('mediator_id');

            // Strategy selector
            $table->string('method', 50); // stripe, mercadopago, paypal, etc.
            $table->string('status', 50); // pending, paid, failed, expired

            // Money in minor units (cents)
            $table->unsignedInteger('amount_total');
            $table->char('currency', 3);

            // Provider identifiers
            $table->string('provider_session_id')->nullable()->index();
            $table->string('provider_payment_intent_id')->nullable()->index();

            // Business fields
            $table->text('topic')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['method', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_payments');
    }
};
