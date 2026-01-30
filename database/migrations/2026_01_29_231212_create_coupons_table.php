<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 6)->unique(); // 6 chars uppercase specific requirement
            $table->integer('discount_percentage'); // 25, 50, 75, 100
            $table->timestamp('expires_at');
            $table->integer('max_uses_per_user')->default(1); // 1, 2, 3
            $table->string('allowed_users_type')->default('all'); // 'all', 'selected'
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
