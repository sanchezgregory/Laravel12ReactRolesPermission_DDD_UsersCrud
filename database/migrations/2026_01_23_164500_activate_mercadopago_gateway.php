<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('payment_gateways')
            ->where('slug', 'mercadopago')
            ->update(['is_active' => true]);

        DB::table('payment_gateways')
            ->where('slug', 'stripe')
            ->update(['is_active' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('payment_gateways')
            ->where('slug', 'mercadopago')
            ->update(['is_active' => false]);

        DB::table('payment_gateways')
            ->where('slug', 'stripe')
            ->update(['is_active' => true]);
    }
};
