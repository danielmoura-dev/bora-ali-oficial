<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->enum('payment_provider', ['mercadopago', 'pagarme'])
                ->default('mercadopago')
                ->after('is_free');

            $table->enum('payment_mode', ['split', 'direct'])
                ->default('direct')
                ->after('payment_provider');

            // JSON sem default — o default vai no model
            $table->json('payment_methods')
                ->nullable()
                ->after('payment_mode');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['payment_provider', 'payment_mode', 'payment_methods']);
        });
    }
};