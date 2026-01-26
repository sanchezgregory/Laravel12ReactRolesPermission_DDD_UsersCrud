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
        Schema::table('session_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('mediator_session_id')->nullable()->after('mediator_id');
            $table->foreign('mediator_session_id')->references('id')->on('mediator_sessions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('session_payments', function (Blueprint $table) {
            $table->dropForeign(['mediator_session_id']);
            $table->dropColumn('mediator_session_id');
        });
    }
};
