<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();

            $table->string('reference')->unique();           // Código único do pedido
            $table->unsignedBigInteger('subtotal');          // Em centavos
            $table->unsignedBigInteger('platform_fee');      // R$ 1,00 = 100 centavos por item
            $table->unsignedBigInteger('total');             // subtotal + platform_fee

            $table->enum('status', [
                'pending',      // Aguardando pagamento
                'paid',         // Pago
                'cancelled',    // Cancelado
                'refunded',     // Reembolsado
            ])->default('pending');

            $table->string('payment_method')->nullable();    // credit_card, pix, boleto
            $table->string('payment_id')->nullable();        // ID externo do Pagar.me
            $table->json('payment_metadata')->nullable();    // Dados extras do gateway

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};