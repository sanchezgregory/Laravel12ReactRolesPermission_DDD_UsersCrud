<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mediator_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            // Precio por sesión (V1): amount en centavos
            $table->unsignedInteger('session_price_minor')->default(0);
            $table->char('currency', 3)->default('EUR');

            $table->string('calendly_url')->nullable();

            // Campos opcionales para UI
            $table->string('headline')->nullable();
            $table->text('bio')->nullable();

            $table->timestamps();

            $table->index(['currency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mediator_profiles');
    }
};
