<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('checked_in_by')->constrained('users')->cascadeOnDelete();
            $table->string('ticket_code');
            $table->timestamp('checked_in_at');
            $table->timestamps();

            $table->unique('ticket_code'); // cada ingresso só faz check-in uma vez
            $table->index('event_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkins');
    }
};