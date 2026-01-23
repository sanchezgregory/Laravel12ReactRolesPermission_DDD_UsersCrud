<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique(); // stripe, mercadopago
            $table->boolean('is_active')->default(false);
            $table->json('credentials')->nullable();
            $table->timestamps();
        });
        
        // Seeder inicial opcional para que la tabla no esté vacía
        DB::table('payment_gateways')->insert([
            [
                'name' => 'Stripe', 
                'slug' => 'stripe', 
                'is_active' => true, 
                'credentials' => json_encode([])
            ],
            [
                'name' => 'Mercado Pago', 
                'slug' => 'mercadopago', 
                'is_active' => false, // Initial setup as per prompt
                'credentials' => json_encode([])
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
