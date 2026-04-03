<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // snake_case, ex: dress_code
            $table->string('label'); // ex: Dress Code
            $table->enum('type', ['text', 'textarea', 'number', 'select', 'checkbox', 'date'])->default('text');
            $table->json('options')->nullable(); // para campos do tipo select
            $table->string('placeholder')->nullable();
            $table->boolean('required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_fields');
    }
};
