<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete()->after('user_id');
            $table->boolean('absorb_service_fee')->default(false)->after('is_free');
            $table->enum('ticket_nomenclature', ['ingresso', 'inscricao'])->default('ingresso')->after('absorb_service_fee');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'absorb_service_fee', 'ticket_nomenclature']);
        });
    }
};
