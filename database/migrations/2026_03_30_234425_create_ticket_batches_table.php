<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_type_id')->constrained()->cascadeOnDelete();

            $table->string('name');                          // Ex: 1º Lote, 2º Lote
            $table->unsignedInteger('quantity');             // Total de ingressos
            $table->unsignedInteger('quantity_sold')->default(0);
            $table->unsignedBigInteger('price');             // Em centavos
            $table->dateTime('starts_at')->nullable();       // Início da venda
            $table->dateTime('ends_at')->nullable();         // Fim da venda
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_batches');
    }
};